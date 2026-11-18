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

        // ============================================================
        // --- ANIMAUX À ADOPTER (6) ---
        // ============================================================
        
        // 1. Rocky
        $art1 = new Article();
        $art1->setNom('Rocky - Golden Retriever Joyeux');
        $art1->setUrlSimplifiee('rocky-golden-retriever');
        $art1->setDescription("Rocky est un magnifique Golden Retriever plein d'énergie, joueur et extrêmement affectueux avec les enfants. Il adore les longues balades en forêt et les plongeons dans l'eau. Adoption responsable sous contrat associatif.");
        $art1->setPrix(35000); // 350,00 €
        $art1->setStock(1);
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

        // 2. Mimi
        $art2 = new Article();
        $art2->setNom('Mimi - Chatte Câline Européenne');
        $art2->setUrlSimplifiee('mimi-chatte-caline');
        $art2->setDescription("Mimi est une petite perle ronronnante qui adore faire la sieste sur les genoux. Très propre, curieuse et sociable, elle fera le bonheur d'un foyer calme et aimant.");
        $art2->setPrix(18000); // 180,00 €
        $art2->setStock(1);
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

        // 3. Barnabé
        $art3 = new Article();
        $art3->setNom('Barnabé - Lapin Bélier Nain');
        $art3->setUrlSimplifiee('barnabe-lapin-belier');
        $art3->setDescription("Barnabé aux grandes oreilles tombantes est habitué à la liberté surveillée. Gourmand de foin de Crau et de pissenlits, il est doux et propre en litière.");
        $art3->setPrix(9500); // 95,00 €
        $art3->setStock(1);
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

        // 4. Coco le Perroquet / Perruche
        $artCoco = new Article();
        $artCoco->setNom('Coco - Perruche Ondulée Chanteuse');
        $artCoco->setUrlSimplifiee('coco-perruche-ondulee');
        $artCoco->setDescription("Coco est une magnifique perruche ondulée au plumage vert et jaune vif. Sociable, curieuse et très joyeuse, elle siffle des mélodies entraînantes dès le réveil.");
        $artCoco->setPrix(4500); // 45,00 €
        $artCoco->setStock(1);
        $artCoco->setImage('https://images.unsplash.com/photo-1552728089-57bdde30beb3?auto=format&fit=crop&w=800&q=80');
        $artCoco->setBadge('ADOPTION');
        $artCoco->setDisponible(true);
        $artCoco->setCategorie($categories['animaux']);
        $manager->persist($artCoco);

        $animalCoco = new Animal();
        $animalCoco->setEspece('Oiseau');
        $animalCoco->setRace('Perruche Ondulée');
        $animalCoco->setSexe('Mâle');
        $animalCoco->setDateNaissance(new \DateTime('2024-05-12'));
        $animalCoco->setNumeroPuce('250269608993322');
        $animalCoco->setVaccine(true);
        $animalCoco->setSterilise(false);
        $animalCoco->setArticle($artCoco);
        $artCoco->setAnimal($animalCoco);
        $manager->persist($animalCoco);

        // 5. Simba le Chaton Roux
        $artSimba = new Article();
        $artSimba->setNom('Simba - Chaton Roux Espiègle');
        $artSimba->setUrlSimplifiee('simba-chaton-roux');
        $artSimba->setDescription("Simba est une véritable boule d'énergie ronronnante. Toujours prêt à courir après une balle ou à réclamer un câlin sur l'épaule. Primo-vacciné et stérilisé.");
        $artSimba->setPrix(16000); // 160,00 €
        $artSimba->setStock(1);
        $artSimba->setImage('https://images.unsplash.com/photo-1574158622682-e40e69881006?auto=format&fit=crop&w=800&q=80');
        $artSimba->setBadge('COUP DE COEUR');
        $artSimba->setDisponible(true);
        $artSimba->setCategorie($categories['animaux']);
        $manager->persist($artSimba);

        $animalSimba = new Animal();
        $animalSimba->setEspece('Chat');
        $animalSimba->setRace('Européen Roux Tabby');
        $animalSimba->setSexe('Mâle');
        $animalSimba->setDateNaissance(new \DateTime('2024-07-20'));
        $animalSimba->setNumeroPuce('250269608998877');
        $animalSimba->setVaccine(true);
        $animalSimba->setSterilise(true);
        $animalSimba->setArticle($artSimba);
        $artSimba->setAnimal($animalSimba);
        $manager->persist($animalSimba);

        // 6. Bella le Bouledogue Français
        $artBella = new Article();
        $artBella->setNom('Bella - Bouledogue Français Dorloteuse');
        $artBella->setUrlSimplifiee('bella-bouledogue-francais');
        $artBella->setDescription("Bella est une petite reine des canapés et des ronflements affectueux. Douce, calme en appartement et adore les gratouilles derrière les oreilles.");
        $artBella->setPrix(38000); // 380,00 €
        $artBella->setStock(1);
        $artBella->setImage('https://images.unsplash.com/photo-1583511655857-d19b40a7a54e?auto=format&fit=crop&w=800&q=80');
        $artBella->setBadge('ADOPTION');
        $artBella->setDisponible(true);
        $artBella->setCategorie($categories['animaux']);
        $manager->persist($artBella);

        $animalBella = new Animal();
        $animalBella->setEspece('Chien');
        $animalBella->setRace('Bouledogue Français Caille');
        $animalBella->setSexe('Femelle');
        $animalBella->setDateNaissance(new \DateTime('2023-11-05'));
        $animalBella->setNumeroPuce('250269608994433');
        $animalBella->setVaccine(true);
        $animalBella->setSterilise(true);
        $animalBella->setArticle($artBella);
        $artBella->setAnimal($animalBella);
        $manager->persist($animalBella);

        // ============================================================
        // --- ALIMENTATION (6) ---
        // ============================================================

        // 7. Croquettes Saumon
        $art4 = new Article();
        $art4->setNom('Croquettes Saumon Sauvage & Patate Douce');
        $art4->setUrlSimplifiee('croquettes-saumon-patate-douce');
        $art4->setDescription("Recette sans céréales riche en oméga-3 pour un poil éclatant et une digestion optimale. Idéale pour chiens et chiots sensibles. Sac de 5kg.");
        $art4->setPrix(4290); // 42,90 €
        $art4->setStock(45);
        $art4->setImage('https://images.unsplash.com/photo-1589924691995-400dc9ecc119?auto=format&fit=crop&w=800&q=80');
        $art4->setBadge('BEST-SELLER');
        $art4->setDisponible(true);
        $art4->setCategorie($categories['alimentation']);
        $manager->persist($art4);

        // 8. Pâtée Poulet Bio
        $art5 = new Article();
        $art5->setNom('Pâtée Gourmet Émincés Poulet Bio');
        $art5->setUrlSimplifiee('patee-gourmet-poulet-bio');
        $art5->setDescription("Délicieuse terrine artisanale mitonnée au poulet fermier bio et petits légumes croquants. Sans conservateurs ni colorants artificiels. Lot de 6 boîtes.");
        $art5->setPrix(1890); // 18,90 €
        $art5->setStock(80);
        $art5->setImage('https://images.unsplash.com/photo-1583337130417-3346a1be7dee?auto=format&fit=crop&w=800&q=80');
        $art5->setBadge('BIO');
        $art5->setDisponible(true);
        $art5->setCategorie($categories['alimentation']);
        $manager->persist($art5);

        // 9. Friandises Canard
        $art6 = new Article();
        $art6->setNom('Friandises Moelleuses Canard & Myrtilles');
        $art6->setUrlSimplifiee('friandises-canard-myrtilles');
        $art6->setDescription("Récompenses saines riches en antioxydants, parfaites pour l'apprentissage et le renforcement positif des petits et grands aventuriers.");
        $art6->setPrix(850); // 8,50 €
        $art6->setStock(120);
        $art6->setImage('https://images.unsplash.com/photo-1601758228041-f3b2795255f1?auto=format&fit=crop&w=800&q=80');
        $art6->setBadge('POPULAIRE');
        $art6->setDisponible(true);
        $art6->setCategorie($categories['alimentation']);
        $manager->persist($art6);

        // 10. Terrine Dinde & Potiron
        $artTerrine = new Article();
        $artTerrine->setNom('Terrine Gourmande Dinde & Potiron Délicieux');
        $artTerrine->setUrlSimplifiee('terrine-dinde-potiron');
        $artTerrine->setDescription("Riche en fibres douces et en viandes fraîches pour préserver la flore intestinale et régaler les palais les plus exigeants. Lot de 4 pots en verre.");
        $artTerrine->setPrix(1450); // 14,50 €
        $artTerrine->setStock(65);
        $artTerrine->setImage('https://images.unsplash.com/photo-1568640347023-a616a30bc3bd?auto=format&fit=crop&w=800&q=80');
        $artTerrine->setBadge('NOUVEAU');
        $artTerrine->setDisponible(true);
        $artTerrine->setCategorie($categories['alimentation']);
        $manager->persist($artTerrine);

        // 11. Bâtonnets Dentaires
        $artDent = new Article();
        $artDent->setNom('Bâtonnets Croustillants Dentaires à la Menthe');
        $artDent->setUrlSimplifiee('batonnets-dentaires-menthe');
        $artDent->setDescription("Réduit la plaque dentaire et combat le tartre tout en offrant une haleine fraîche et saine. 100% végétal et facile à digérer.");
        $artDent->setPrix(790); // 7,90 €
        $artDent->setStock(95);
        $artDent->setImage('https://images.unsplash.com/photo-1541599540903-216a46ca1dc0?auto=format&fit=crop&w=800&q=80');
        $artDent->setBadge('HYGIÈNE');
        $artDent->setDisponible(true);
        $artDent->setCategorie($categories['alimentation']);
        $manager->persist($artDent);

        // 12. Huile de Saumon
        $artHuile = new Article();
        $artHuile->setNom('Huile Pure de Saumon Sauvage de Norvège (500ml)');
        $artHuile->setUrlSimplifiee('huile-saumon-norvege');
        $artHuile->setDescription("Pressée à froid en Norvège, gorgée d'acides gras essentiels EPA et DHA. Redonne brillance au pelage terne et soutient les articulations.");
        $artHuile->setPrix(2190); // 21,90 €
        $artHuile->setStock(40);
        $artHuile->setImage('https://images.unsplash.com/photo-1471864190281-a93a3070b6de?auto=format&fit=crop&w=800&q=80');
        $artHuile->setBadge('SANTÉ');
        $artHuile->setDisponible(true);
        $artHuile->setCategorie($categories['alimentation']);
        $manager->persist($artHuile);

        // ============================================================
        // --- LOISIRS & JOUETS (6) ---
        // ============================================================

        // 13. Balle Fluo
        $art7 = new Article();
        $art7->setNom('Balle Rebondissante Fluo Ultra-Résistante');
        $art7->setUrlSimplifiee('balle-rebondissante-fluo');
        $art7->setDescription("Fabriquée en caoutchouc naturel indestructible, flotte dans l'eau et rebondit de manière imprévisible pour stimuler l'instinct de chasse.");
        $art7->setPrix(1290); // 12,90 €
        $art7->setStock(35);
        $art7->setOptions(['Taille S (chiots/chats)', 'Taille L (adultes)']);
        $art7->setImage('https://images.unsplash.com/photo-1576201836106-db1758fd1c97?auto=format&fit=crop&w=800&q=80');
        $art7->setBadge('NOUVEAU');
        $art7->setDisponible(true);
        $art7->setCategorie($categories['loisirs']);
        $manager->persist($art7);

        // 14. Corde à Nœuds
        $art8 = new Article();
        $art8->setNom('Corde à Nœuds Multicolore XXL');
        $art8->setUrlSimplifiee('corde-a-noeuds-multicolore');
        $art8->setDescription("Corde de traction robuste en coton naturel qui nettoie les dents et masse les gencives tout en s'amusant. Longueur 65cm.");
        $art8->setPrix(1550); // 15,50 €
        $art8->setStock(50);
        $art8->setImage('https://images.unsplash.com/photo-1535930891776-0c2dfb7fda1a?auto=format&fit=crop&w=800&q=80');
        $art8->setBadge('TOP JOUET');
        $art8->setDisponible(true);
        $art8->setCategorie($categories['loisirs']);
        $manager->persist($art8);

        // 15. Canne à Pêche Laser
        $art9 = new Article();
        $art9->setNom('Canne à Pêche Laser & Plumes Magiques');
        $art9->setUrlSimplifiee('canne-a-peche-plumes-laser');
        $art9->setDescription("Le jouet interactif numéro 1 des félins ! Tige télescopique avec plumes naturelles vibrantes et pointeur lumineux doux intégré.");
        $art9->setPrix(1990); // 19,90 €
        $art9->setStock(60);
        $art9->setImage('https://images.unsplash.com/photo-1545249390-6bdfa286032f?auto=format&fit=crop&w=800&q=80');
        $art9->setBadge('COUP DE COEUR');
        $art9->setDisponible(true);
        $art9->setCategorie($categories['loisirs']);
        $manager->persist($art9);

        // 16. Arbre à Chat Néo-Brutaliste
        $artArbre = new Article();
        $artArbre->setNom('Arbre à Chat Pop Tour Néo-Brutaliste');
        $artArbre->setUrlSimplifiee('arbre-a-chat-pop-tour');
        $artArbre->setDescription("Structure solide en bois certifié et poteaux en sisal naturel épais. Comprend 3 plateformes pastel, un hamac douillet et une cachette secrète.");
        $artArbre->setPrix(7990); // 79,90 €
        $artArbre->setStock(15);
        $artArbre->setImage('https://images.unsplash.com/photo-1541781774459-bb2af2f05b55?auto=format&fit=crop&w=800&q=80');
        $artArbre->setBadge('DESIGN');
        $artArbre->setDisponible(true);
        $artArbre->setCategorie($categories['loisirs']);
        $manager->persist($artArbre);

        // 17. Tapis de Fouille Snuffle Mat
        $artTapis = new Article();
        $artTapis->setNom('Tapis de Fouille Snuffle Mat Multicolore');
        $artTapis->setUrlSimplifiee('tapis-de-fouille-multicolore');
        $artTapis->setDescription("Stimule le flair et réduit le stress en incitant l'animal à chercher ses croquettes parmi les lanières de feutrine douce. Lavable en machine.");
        $artTapis->setPrix(2490); // 24,90 €
        $artTapis->setStock(35);
        $artTapis->setOptions(['Standard (50x50cm)', 'Maxi XXL (70x70cm)']);
        $artTapis->setImage('https://images.unsplash.com/photo-1518717758536-85ae29035b6d?auto=format&fit=crop&w=800&q=80');
        $artTapis->setBadge('ÉVEIL');
        $artTapis->setDisponible(true);
        $artTapis->setCategorie($categories['loisirs']);
        $manager->persist($artTapis);

        // 18. Frisbee Fluo
        $artFrisbee = new Article();
        $artFrisbee->setNom('Frisbee Fluo Aérodynamique Souple');
        $artFrisbee->setUrlSimplifiee('frisbee-fluo-aerodynamique');
        $artFrisbee->setDescription("Vol plané parfait et matière silicone ultra-souple pour rattraper le disque en plein vol sans jamais blesser les gencives.");
        $artFrisbee->setPrix(1190); // 11,90 €
        $artFrisbee->setStock(50);
        $artFrisbee->setImage('https://images.unsplash.com/photo-1568640347023-a616a30bc3bd?auto=format&fit=crop&w=800&q=80');
        $artFrisbee->setBadge('EXTÉRIEUR');
        $artFrisbee->setDisponible(true);
        $artFrisbee->setCategorie($categories['loisirs']);
        $manager->persist($artFrisbee);

        // ============================================================
        // --- FASHION & LOOKS (6) ---
        // ============================================================

        // 19. Harnais Réfléchissant
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

        // 20. Bandana Polka Dots
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

        // 21. Pull Maille Pastel
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

        // 22. Ciré Jaune Marin
        $artCire = new Article();
        $artCire->setNom('Imperméable Jaune Ciré Marin pour Toutou');
        $artCire->setUrlSimplifiee('cire-marin-jaune-toutou');
        $artCire->setDescription("Ne craignez plus les averses d'automne ! Tissu 100% déperlant coupe-vent avec doublure rayée marinette et passage pour la laisse.");
        $artCire->setPrix(3290); // 32,90 €
        $artCire->setStock(30);
        $artCire->setOptions(['XS (petit chiot)', 'S', 'M', 'L', 'XL']);
        $artCire->setImage('https://images.unsplash.com/photo-1576201836106-db1758fd1c97?auto=format&fit=crop&w=800&q=80');
        $artCire->setBadge('COUP DE COEUR');
        $artCire->setDisponible(true);
        $artCire->setCategorie($categories['fashion']);
        $manager->persist($artCire);

        // 23. Collier Néo-Pop
        $artCollier = new Article();
        $artCollier->setNom('Collier Néo-Pop avec Boucle Métal Noir');
        $artCollier->setUrlSimplifiee('collier-neo-pop-boucle');
        $artCollier->setDescription("Sangle haute densité ultra-résistante aux couleurs néo-brutalistes avec boucle métallique satinée et anneau d'attache soudé.");
        $artCollier->setPrix(1890); // 18,90 €
        $artCollier->setStock(45);
        $artCollier->setOptions(['Turquoise Menthe', 'Corail Pop', 'Lavande Électrique']);
        $artCollier->setImage('https://images.unsplash.com/photo-1601758228041-f3b2795255f1?auto=format&fit=crop&w=800&q=80');
        $artCollier->setBadge('TENDANCE');
        $artCollier->setDisponible(true);
        $artCollier->setCategorie($categories['fashion']);
        $manager->persist($artCollier);

        // 24. Chaussettes Anti-Glisse
        $artChaussettes = new Article();
        $artChaussettes->setNom('Chaussettes Anti-Glisse Mignonnes (Lot de 4)');
        $artChaussettes->setUrlSimplifiee('chaussettes-anti-glisse-lot4');
        $artChaussettes->setDescription("Avec semelles picots en caoutchouc antidérapant, parfaites pour protéger les pattes blessées et éviter les glissades sur carrelage.");
        $artChaussettes->setPrix(990); // 9,90 €
        $artChaussettes->setStock(60);
        $artChaussettes->setOptions(['S (chats/chiots)', 'M (moyens)', 'L (grands)']);
        $artChaussettes->setImage('https://images.unsplash.com/photo-1535930891776-0c2dfb7fda1a?auto=format&fit=crop&w=800&q=80');
        $artChaussettes->setBadge('POPULAIRE');
        $artChaussettes->setDisponible(true);
        $artChaussettes->setCategorie($categories['fashion']);
        $manager->persist($artChaussettes);

        $manager->flush();
    }
}
