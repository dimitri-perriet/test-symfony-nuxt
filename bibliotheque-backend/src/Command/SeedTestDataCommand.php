<?php

namespace App\Command;

use App\Entity\Auteur;
use App\Entity\Categorie;
use App\Entity\Livre;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:seed-test-data',
    description: 'Insère des données de test: 1 admin, 1 user, auteurs, catégories et livres',
)]
class SeedTestDataCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('purge', null, InputOption::VALUE_NONE, 'Purge les données existantes (utilisateurs, livres, auteurs, catégories) avant d\'insérer')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $doPurge = (bool) $input->getOption('purge');

        if ($doPurge) {
            $this->purge($io);
        }

        // Users
        [$admin, $user] = $this->seedUsers($io);

        // Catégories
        $categories = $this->seedCategories($io, [
            ['nom' => 'Science-fiction', 'description' => 'Anticipation, mondes futurs, technologies'],
            ['nom' => 'Fantasy', 'description' => 'Magie, mondes imaginaires, épopées'],
            ['nom' => 'Histoire', 'description' => 'Essais et récits historiques'],
            ['nom' => 'Informatique', 'description' => 'Programmation, systèmes, réseaux'],
            ['nom' => 'Policier', 'description' => 'Enquêtes, thrillers'],
        ]);

        // Auteurs
        $auteurs = $this->seedAuteurs($io, [
            ['prenom' => 'Isaac', 'nom' => 'Asimov', 'biographie' => 'Auteur prolifique de science-fiction.', 'naissance' => '1920-01-02', 'deces' => '1992-04-06'],
            ['prenom' => 'J.R.R.', 'nom' => 'Tolkien', 'biographie' => 'Philologue et écrivain, créateur de la Terre du Milieu.', 'naissance' => '1892-01-03', 'deces' => '1973-09-02'],
            ['prenom' => 'George', 'nom' => 'Orwell', 'biographie' => 'Essayiste et romancier anglais.', 'naissance' => '1903-06-25', 'deces' => '1950-01-21'],
            ['prenom' => 'Robert C.', 'nom' => 'Martin', 'biographie' => 'Ingénieur logiciel, auteur de Clean Code.', 'naissance' => '1952-12-05', 'deces' => null],
        ]);

        // Livres (quelques vrais titres)
        $livresSpec = [
            ['titre' => 'Foundation', 'isbn' => '9780553293357', 'resume' => 'L\'effondrement de l\'Empire Galactique et la psychohistoire.', 'categorie' => 'Science-fiction', 'auteur' => 'Isaac Asimov', 'proprietaire' => 'admin'],
            ['titre' => 'Le Seigneur des Anneaux', 'isbn' => '9782266134837', 'resume' => 'Épopée en Terre du Milieu.', 'categorie' => 'Fantasy', 'auteur' => 'J.R.R. Tolkien', 'proprietaire' => 'user'],
            ['titre' => '1984', 'isbn' => '9780451524935', 'resume' => 'Dystopie sur la surveillance et le totalitarisme.', 'categorie' => 'Science-fiction', 'auteur' => 'George Orwell', 'proprietaire' => 'admin'],
            ['titre' => 'Clean Code', 'isbn' => '9780132350884', 'resume' => 'Guide de bonnes pratiques de programmation.', 'categorie' => 'Informatique', 'auteur' => 'Robert C. Martin', 'proprietaire' => 'user'],
        ];

        $this->seedLivres($io, $livresSpec, $categories, $auteurs, $admin, $user);

        $io->success('Données de test insérées (ou mises à jour) avec succès.');
        return Command::SUCCESS;
    }

    private function purge(SymfonyStyle $io): void
    {
        $conn = $this->entityManager->getConnection();
        $platform = $conn->getDatabasePlatform();
        $io->note('Purge des tables (TRUNCATE)');
        $conn->executeStatement('SET session_replication_role = replica');
        foreach (['livre', 'auteur', 'categorie', '"user"'] as $table) {
            $conn->executeStatement($platform->getTruncateTableSQL($table, true));
        }
        $conn->executeStatement('SET session_replication_role = DEFAULT');
    }

    /**
     * @return array{0: User, 1: User}
     */
    private function seedUsers(SymfonyStyle $io): array
    {
        $repo = $this->entityManager->getRepository(User::class);

        $admin = $repo->findOneBy(['email' => 'admin@example.com']);
        if (!$admin) {
            $admin = new User();
            $admin->setEmail('admin@example.com');
            $admin->setNom('Admin');
            $admin->setPrenom('Super');
            $admin->setRoles(['ROLE_ADMIN']);
            $admin->setPassword($this->passwordHasher->hashPassword($admin, 'adminpass'));
            $this->entityManager->persist($admin);
            $io->writeln('Utilisateur admin créé: admin@example.com / adminpass');
        }

        $user = $repo->findOneBy(['email' => 'user@example.com']);
        if (!$user) {
            $user = new User();
            $user->setEmail('user@example.com');
            $user->setNom('User');
            $user->setPrenom('Demo');
            $user->setRoles(['ROLE_USER']);
            $user->setPassword($this->passwordHasher->hashPassword($user, 'userpass'));
            $this->entityManager->persist($user);
            $io->writeln('Utilisateur user créé: user@example.com / userpass');
        }

        $this->entityManager->flush();
        return [$admin, $user];
    }

    /**
     * @param list<array{nom: string, description: string}> $spec
     * @return array<string, Categorie>
     */
    private function seedCategories(SymfonyStyle $io, array $spec): array
    {
        $repo = $this->entityManager->getRepository(Categorie::class);
        $result = [];
        foreach ($spec as $item) {
            $categorie = $repo->findOneBy(['nom' => $item['nom']]);
            if (!$categorie) {
                $categorie = new Categorie();
                $categorie->setNom($item['nom']);
                $categorie->setDescription($item['description']);
                // La couleur est générée automatiquement dans le constructeur
                $this->entityManager->persist($categorie);
            }
            $result[$item['nom']] = $categorie;
        }
        $this->entityManager->flush();
        return $result;
    }

    /**
     * @param list<array{prenom: string, nom: string, biographie: string, naissance: ?string, deces: ?string}> $spec
     * @return array<string, Auteur>
     */
    private function seedAuteurs(SymfonyStyle $io, array $spec): array
    {
        $repo = $this->entityManager->getRepository(Auteur::class);
        $result = [];
        foreach ($spec as $item) {
            $key = trim($item['prenom'] . ' ' . $item['nom']);
            $auteur = $repo->findOneBy(['nom' => $item['nom'], 'prenom' => $item['prenom']]);
            if (!$auteur) {
                $auteur = new Auteur();
                $auteur->setNom($item['nom']);
                $auteur->setPrenom($item['prenom']);
                $auteur->setBiographie($item['biographie']);
                if (!empty($item['naissance'])) {
                    $auteur->setDateNaissance(new \DateTime($item['naissance']));
                }
                if (!empty($item['deces'])) {
                    $auteur->setDateDeces(new \DateTime($item['deces']));
                }
                $this->entityManager->persist($auteur);
            }
            $result[$key] = $auteur;
        }
        $this->entityManager->flush();
        return $result;
    }

    /**
     * @param list<array{titre: string, isbn: string, resume: string, categorie: string, auteur: string, proprietaire: 'admin'|'user'}> $spec
     * @param array<string, Categorie> $categories
     * @param array<string, Auteur> $auteurs
     */
    private function seedLivres(SymfonyStyle $io, array $spec, array $categories, array $auteurs, User $admin, User $user): void
    {
        $repo = $this->entityManager->getRepository(Livre::class);
        foreach ($spec as $item) {
            $livre = $repo->findOneBy(['isbn' => $item['isbn']]);
            if (!$livre) {
                $livre = new Livre();
                $livre->setIsbn($item['isbn']);
            }
            $livre->setTitre($item['titre']);
            $livre->setResume($item['resume']);
            $livre->setDatePublication(new \DateTime('2000-01-01'));

            $categorie = $categories[$item['categorie']] ?? null;
            if ($categorie) {
                $livre->setCategorie($categorie);
            }

            // auteur key correspond à "Prenom Nom"
            $auteurKey = $item['auteur'];
            // normaliser quelques variantes (ex: Tolkien clé ci-dessus)
            foreach ($auteurs as $k => $val) {
                if (stripos($k, $auteurKey) !== false) {
                    $auteurKey = $k;
                    break;
                }
            }
            $auteur = $auteurs[$auteurKey] ?? null;
            if ($auteur) {
                $livre->setAuteur($auteur);
            }

            $proprio = $item['proprietaire'] === 'admin' ? $admin : $user;
            $livre->setProprietaire($proprio);

            $this->entityManager->persist($livre);
        }
        $this->entityManager->flush();
    }
}


