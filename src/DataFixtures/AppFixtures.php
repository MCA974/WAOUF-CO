<?php

namespace App\DataFixtures;

use App\Entity\Animal;
use App\Entity\Article;
use App\Entity\Categorie;
use App\Entity\Utilisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        // 1. Catégories officielles
        $categoriesData = [
            'animaux' => [
                'nom' => 'Animaux',
                'url' => 'animaux',
                'couleur' => '#FF6B6B',
            ],
            'alimentation' => [
                'nom' => 'Alimentation',
                'url' => 'alimentation',
                'couleur' => '#4ECDC4',
            ],
            'loisirs' => [
                'nom' => 'Loisirs',
                'url' => 'loisirs',
                'couleur' => '#FFE66D',
            ],
            'fashion' => [
                'nom' => 'Fashion',
                'url' => 'fashion',
                'couleur' => '#A29BFE',
            ],
        ];

        $categories = [];
        foreach ($categoriesData as $key => $data) {
            $cat = new Categorie();
            $cat->setNom($data['nom']);
            $cat->setUrlSimplifiee($data['url']);
            $cat->setCouleurHex($data['couleur']);
            $manager->persist($cat);
            $categories[$key] = $cat;
        }

        // 2. Utilisateurs
        $admin = new Utilisateur();
        $admin->setEmail('admin@waouf.fr');
        $admin->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
        $admin->setPrenom('Maxime');
        $admin->setNom('Directeur');
        $admin->setTelephone('0612345678');
        $admin->setAdresse("42 Rue des Ouafs, 75011 Paris");
        $admin->setMotDePasse($this->passwordHasher->hashPassword($admin, 'Admin123!'));
        $manager->persist($admin);

        $client = new Utilisateur();
        $client->setEmail('camille@waouf.fr');
        $client->setRoles(['ROLE_USER']);
        $client->setPrenom('Camille');
        $client->setNom('Dupont');
        $client->setTelephone('0698765432');
        $client->setAdresse("15 Avenue des Croquettes, 69002 Lyon");
        $client->setMotDePasse($this->passwordHasher->hashPassword($client, 'Client123!'));
        $manager->persist($client);

        // 3. Articles & Animaux

        // --- ANIMAUX (3) ---
        $art1 = new Article();
        $art1->setNom('Rocky - Golden Retriever Joyeux');
        $art1->setUrlSimplifiee('rocky-golden-retriever');
        $art1->setDescription("Rocky est un magnifique Golden Retriever plein d'énergie, joueur et extrêmement affectueux avec les enfants. Il adore les longues balades en forêt et les plongeons dans l'eau. Adoption responsable sous contrat associatif.");
        $art1->setPrix(35000); // 350,00 €
        $art1->setStock(1);
        $art1->setOptions(null);
        $art1->setImage('https://images.unsplash.com/photo-1552053831-71594a27632d?auto=format&fit=crop&w=800&q=80');
        $art1->setBadge('ADOPTION');
        $art1->setDisponible(true);
        $art1->setCategorie($categories['animaux']);
        $manager->persist($art1);

        $animal1 = new Animal();
        $animal1->setEspece('Chien');
        $animal1->setRace('Golden Retriever');
        $animal1->setSexe('Mâle');
        $animal1->setDateNaissance(new \DateTime('2024-04-15'));
        $animal1->setNumeroPuce('250269608991234');
        $animal1->setVaccine(true);
        $animal1->setSterilise(true);
        $animal1->setArticle($art1);
        $art1->setAnimal($animal1);
        $manager->persist($animal1);

        $art2 = new Article();
        $art2->setNom('Mimi - Chatte Câline Européenne');
        $art2->setUrlSimplifiee('mimi-chatte-caline');
        $art2->setDescription("Mimi est une petite perle ronronnante qui adore faire la sieste sur les genoux. Très propre, curieuse et sociable, elle fera le bonheur d'un foyer calme et aimant.");
        $art2->setPrix(18000); // 180,00 €
        $art2->setStock(1);
        $art2->setOptions(null);
        $art2->setImage('https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?auto=format&fit=crop&w=800&q=80');
        $art2->setBadge('COUP DE COEUR');
        $art2->setDisponible(true);
        $art2->setCategorie($categories['animaux']);
        $manager->persist($art2);

        $animal2 = new Animal();
        $animal2->setEspece('Chat');
        $animal2->setRace('Européen Tigré');
        $animal2->setSexe('Femelle');
        $animal2->setDateNaissance(new \DateTime('2024-06-10'));
        $animal2->setNumeroPuce('250269608995678');
        $animal2->setVaccine(true);
        $animal2->setSterilise(true);
        $animal2->setArticle($art2);
        $art2->setAnimal($animal2);
        $manager->persist($animal2);

        $art3 = new Article();
        $art3->setNom('Barnabé - Lapin Bélier Nain');
        $art3->setUrlSimplifiee('barnabe-lapin-belier');
        $art3->setDescription("Barnabé aux grandes oreilles tombantes est habitué à la liberté surveillée. Gourmand de foin de Crau et de pissenlits, il est doux et propre en litière.");
        $art3->setPrix(9500); // 95,00 €
        $art3->setStock(1);
        $art3->setOptions(null);
        $art3->setImage('https://images.unsplash.com/photo-1585110396000-c9ffd4e4b308?auto=format&fit=crop&w=800&q=80');
        $art3->setBadge('ADOPTION');
        $art3->setDisponible(true);
        $art3->setCategorie($categories['animaux']);
        $manager->persist($art3);

        $animal3 = new Animal();
        $animal3->setEspece('Lapin');
        $animal3->setRace('Bélier Nain');
        $animal3->setSexe('Mâle');
        $animal3->setDateNaissance(new \DateTime('2024-09-01'));
        $animal3->setNumeroPuce('250269608999012');
        $animal3->setVaccine(true);
        $animal3->setSterilise(true);
        $animal3->setArticle($art3);
        $art3->setAnimal($animal3);
        $manager->persist($animal3);

        // --- ALIMENTATION (3) ---
        $art4 = new Article();
        $art4->setNom('Croquettes Saumon Sauvage & Patate Douce');
        $art4->setUrlSimplifiee('croquettes-saumon-patate-douce');
        $art4->setDescription("Recette sans céréales riche en oméga-3 pour un poil éclatant et une digestion optimale. Idéale pour chiens et chiots sensibles. Sac de 5kg.");
        $art4->setPrix(4290); // 42,90 €
        $art4->setStock(45);
        $art4->setOptions(null);
        $art4->setImage('https://images.unsplash.com/photo-1589924691995-400dc9ecc119?auto=format&fit=crop&w=800&q=80');
        $art4->setBadge('BEST-SELLER');
        $art4->setDisponible(true);
        $art4->setCategorie($categories['alimentation']);
        $manager->persist($art4);

        $art5 = new Article();
        $art5->setNom('Pâtée Gourmet Émincés Poulet Bio');
        $art5->setUrlSimplifiee('patee-gourmet-poulet-bio');
        $art5->setDescription("Délicieuse terrine artisanale mitonnée au poulet fermier bio et petits légumes croquants. Sans conservateurs ni colorants artificiels. Lot de 6 boîtes.");
        $art5->setPrix(1890); // 18,90 €
        $art5->setStock(80);
        $art5->setOptions(null);
        $art5->setImage('https://images.unsplash.com/photo-1583337130417-3346a1be7dee?auto=format&fit=crop&w=800&q=80');
        $art5->setBadge('BIO');
        $art5->setDisponible(true);
        $art5->setCategorie($categories['alimentation']);
        $manager->persist($art5);

        $art6 = new Article();
        $art6->setNom('Friandises Moelleuses Canard & Myrtilles');
        $art6->setUrlSimplifiee('friandises-canard-myrtilles');
        $art6->setDescription("Récompenses saines riches en antioxydants, parfaites pour l'apprentissage et le renforcement positif des petits et grands aventuriers.");
        $art6->setPrix(850); // 8,50 €
        $art6->setStock(120);
        $art6->setOptions(null);
        $art6->setImage('https://images.unsplash.com/photo-1601758228041-f3b2795255f1?auto=format&fit=crop&w=800&q=80');
        $art6->setBadge('POPULAIRE');
        $art6->setDisponible(true);
        $art6->setCategorie($categories['alimentation']);
        $manager->persist($art6);

        // --- LOISIRS (3) ---
        $art7 = new Article();
        $art7->setNom('Balle Rebondissante Fluo Ultra-Résistante');
        $art7->setUrlSimplifiee('balle-rebondissante-fluo');
        $art7->setDescription("Fabriquée en caoutchouc naturel indestructible, flotte dans l'eau et rebondit de manière imprévisible pour stimuler l'instinct de chasse.");
        $art7->setPrix(1290); // 12,90 €
        $art7->setStock(35);
        $art7->setOptions(['Taille S (chiots)', 'Taille L (adultes)']);
        $art7->setImage('https://images.unsplash.com/photo-1576201836106-db1758fd1c97?auto=format&fit=crop&w=800&q=80');
        $art7->setBadge('NOUVEAU');
        $art7->setDisponible(true);
        $art7->setCategorie($categories['loisirs']);
        $manager->persist($art7);

        $art8 = new Article();
        $art8->setNom('Corde à Nœuds Multicolore XXL');
        $art8->setUrlSimplifiee('corde-a-noeuds-multicolore');
        $art8->setDescription("Corde de traction robuste en coton naturel qui nettoie les dents et masse les gencives tout en s'amusant. Longueur 65cm.");
        $art8->setPrix(1550); // 15,50 €
        $art8->setStock(50);
        $art8->setOptions(null);
        $art8->setImage('https://images.unsplash.com/photo-1535930891776-0c2dfb7fda1a?auto=format&fit=crop&w=800&q=80');
        $art8->setBadge('TOP JOUET');
        $art8->setDisponible(true);
        $art8->setCategorie($categories['loisirs']);
        $manager->persist($art8);

        $art9 = new Article();
        $art9->setNom('Canne à Pêche Laser & Plumes Magiques');
        $art9->setUrlSimplifiee('canne-a-peche-plumes-laser');
        $art9->setDescription("Le jouet interactif numéro 1 des félins ! Tige télescopique avec plumes naturelles vibrantes et pointeur lumineux doux intégré.");
        $art9->setPrix(1990); // 19,90 €
        $art9->setStock(60);
        $art9->setOptions(null);
        $art9->setImage('https://images.unsplash.com/photo-1545249390-6bdfa286032f?auto=format&fit=crop&w=800&q=80');
        $art9->setBadge('COUP DE COEUR');
        $art9->setDisponible(true);
        $art9->setCategorie($categories['loisirs']);
        $manager->persist($art9);

        // --- FASHION (3) ---
        $art10 = new Article();
        $art10->setNom('Harnais Réfléchissant Pop Confort');
        $art10->setUrlSimplifiee('harnais-reflechissant-pop');
        $art10->setDescription("Ergonomique, anti-traction avec rembourrage respirant et bandes ultra-réfléchissantes pour les promenades nocturnes en toute sécurité.");
        $art10->setPrix(3490); // 34,90 €
        $art10->setStock(25);
        $art10->setOptions(['S (tour 35-45cm)', 'M (tour 45-60cm)', 'L (tour 60-80cm)']);
        $art10->setImage('https://images.unsplash.com/photo-1537151625747-768eb6cf92b2?auto=format&fit=crop&w=800&q=80');
        $art10->setBadge('FASHION');
        $art10->setDisponible(true);
        $art10->setCategorie($categories['fashion']);
        $manager->persist($art10);

        $art11 = new Article();
        $art11->setNom('Bandana Rétro Polka Dots');
        $art11->setUrlSimplifiee('bandana-retro-polka-dots');
        $art11->setDescription("Le style pop néo-brutaliste à l'honneur pour votre toutou ou minou ! Coton bio double épaisseur doux et lavable en machine.");
        $art11->setPrix(1490); // 14,90 €
        $art11->setStock(40);
        $art11->setOptions(['S (petit gabarit)', 'M (moyen / grand gabarit)']);
        $art11->setImage('https://images.unsplash.com/photo-1583511655857-d19b40a7a54e?auto=format&fit=crop&w=800&q=80');
        $art11->setBadge('STYLE');
        $art11->setDisponible(true);
        $art11->setCategorie($categories['fashion']);
        $manager->persist($art11);

        $art12 = new Article();
        $art12->setNom('Pull Hivernal en Maille Pastel');
        $art12->setUrlSimplifiee('pull-hivernal-maille-pastel');
        $art12->setDescription("Pull en tricot extensible ultra doux pour protéger du froid hivernal sans restreindre les mouvements. Finition col roulé tendance.");
        $art12->setPrix(2990); // 29,90 €
        $art12->setStock(20);
        $art12->setOptions(['XS', 'S', 'M', 'L']);
        $art12->setImage('https://images.unsplash.com/photo-1548199973-03cce0bbc87b?auto=format&fit=crop&w=800&q=80');
        $art12->setBadge('TENDANCE');
        $art12->setDisponible(true);
        $art12->setCategorie($categories['fashion']);
        $manager->persist($art12);

        $manager->flush();
    }
}
