# Marketplace — exploitation

## Accès

- Administration : `/marketplace/products`, `/marketplace/sellers`, `/marketplace/orders`, `/marketplace/settings`.
- Inscription vendeur : `/devenir-vendeur` ; connexion : `/login`.
- Vendeur : `/vendeur/products`, `/vendeur/boutique`, `/vendeur/commandes`.
- API publique : `/api/marketplace/products`, `/api/marketplace/options`, `/api/marketplace/orders`.

Les comptes existants gardent leur accès administrateur. Les nouvelles inscriptions créent uniquement des vendeurs. Les vendeurs ne peuvent accéder ni à l'ancien backoffice ni aux produits ou commandes des autres vendeurs. Une boutique suspendue disparaît du catalogue et perd son accès de gestion.

## Comptes clients

Le frontend possède ses comptes clients : `/inscription`, `/connexion`, `/compte` et déconnexion par POST. Un visiteur qui ajoute au panier doit s'inscrire ou se connecter ; le produit et sa quantité sont conservés pour reprendre l'ajout. La commande et la confirmation exigent une session authentifiée. La déconnexion efface le panier et la confirmation de cette session. Les connexions vendeurs et administrateurs restent dans le backoffice.

Les requêtes de création de commande sont transmises uniquement par le serveur frontend avec une clé privée `MARKETPLACE_CHECKOUT_KEY` et l'identifiant du client connecté. Configurer la même clé aléatoire dans les `.env` des deux applications avant un déploiement ; elle ne doit jamais être exposée au navigateur. Les environnements locaux sont configurés. Une commande directe sans cette authentification est refusée par l'API.

## Premier démarrage

1. Déployer le backoffice en premier : le workflow lance la migration additive `2026_11_05_000001_create_marketplace`.
2. Les livres sont copiés dans le catalogue général ; les tables et commandes historiques sont conservées. Le catalogue général devient la source du stock pour les nouvelles ventes. Ne plus utiliser les archives livres pour gérer les produits importés.
3. Dans les paramètres marketplace, renseigner les instructions de paiement à la plateforme et le taux de commission souhaité. Le taux initial est de 0 %, pour ne pas prélever de commission non configurée.
4. Choisir la publication immédiate ou la validation préalable (valeur initiale). Une exception peut être définie pour chaque vendeur.
5. Chaque vendeur renseigne les instructions de paiement de sa boutique pour proposer le paiement direct.
6. Déployer le front. Son `FRONT_API_BASE_URL` doit désigner l'API du backoffice. Les appels sont faits côté serveur ; aucun secret n'est transmis au navigateur.

Le front n'invente aucun produit si l'API est indisponible. Il affiche un état d'erreur et permet de réessayer.

## Stock par boutique

Le tableau de bord vendeur `/vendeur/tableau-de-bord` affiche actuellement une maquette statique avec des indicateurs, graphiques et produits de demonstration. Aucune statistique commerciale reelle n'est chargee dans cet ecran. Le badge Maquette le signale. Le sidebar est partage avec toutes les pages vendeur ; les autres fonctions de gestion restent operationnelles. Le calcul dynamique precedent est conserve pour le raccordement apres validation du design. L'administrateur peut ouvrir un apercu depuis la liste des vendeurs.

Chaque vendeur dispose de `/vendeur/stock`, limité à ses propres produits et mouvements. L'administrateur supervise `/marketplace/stock` ; un lien depuis chaque boutique permet de filtrer sur son stock.

Les entrées et sorties exigent une quantité positive et un motif. La quantité disponible est modifiée sous verrou, sans autoriser de stock négatif. Les réservations à la commande et les restitutions après annulation sont enregistrées dans le même historique. L'historique commence à l'application de la migration `2026_11_05_000002_create_product_stock_movements` ; les anciens mouvements ne sont pas reconstruits.

Le formulaire produit définit le stock initial à la création. Ensuite les quantités se gèrent depuis l'écran stock, pour éviter qu'une modification de fiche écrase une vente intervenue entre-temps.

## Publicités du catalogue

L'administrateur gère `/marketplace/advertisements` : ajout, remplacement de l'image, lien au clic, activation, ordre et suppression. Les vendeurs n'ont pas accès à ces actions. Les images téléversées (JPG, PNG, WebP, 4 Mo maximum) sont conservées sur le disque public ; leur remplacement ou suppression retire le fichier associé.

Le front charge uniquement les annonces actives depuis `/api/marketplace/options`, dans l'ordre choisi. Aucune annonce statique n'est réintroduite après une suppression. Sans lien de destination, le clic ouvre l'image en grand. L'animation du bandeau reste gérée par le front.

## Commandes et paiements manuels

Une commande est répartie en sous-commandes par boutique. Les prix viennent du serveur, les quantités sont contrôlées sous verrou et le stock est réservé dans une transaction. Une clé unique évite une deuxième commande lors d'un nouvel envoi identique.

- Paiement plateforme : commission figée à la commande ; seul l'administrateur confirme le paiement et enregistre le reversement réellement effectué, avec références.
- Paiement direct : commission de 0 ; le vendeur concerné ou l'administrateur peut confirmer le paiement reçu.
- Aucune déclaration du client ne marque une commande payée ; aucun débit ni virement automatique n'est exécuté.
- Une commande impayée non expédiée peut être annulée une seule fois ; le stock est restitué. Les remboursements après paiement sont à traiter manuellement hors application.
- Les frais de livraison sont à convenir avec les vendeurs : ils ne sont pas facturés automatiquement dans cette version.

L'ancien endpoint de création de commande `/api/orders` répond 410 pour empêcher une vente simultanée depuis l'ancien stock librairie. Les commandes historiques restent consultables.

## Vérifications

Dans `back/` : `php artisan test`. Les tests couvrent la migration du catalogue, les droits vendeurs, la modération, les commandes multi-vendeurs, les commissions, les paiements manuels, l'annulation et les retours de stock.

Dans `libfront/librairie/` : `php artisan test`. Les tests utilisent une API simulée pour le catalogue, le panier et la confirmation ; aucun paiement réel n'est envoyé.
