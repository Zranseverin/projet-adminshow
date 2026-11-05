# Marketplace — exploitation

## Accès

- Administration : `/marketplace/products`, `/marketplace/sellers`, `/marketplace/orders`, `/marketplace/settings`.
- Inscription vendeur : `/devenir-vendeur` ; connexion : `/login`.
- Vendeur : `/vendeur/products`, `/vendeur/boutique`, `/vendeur/commandes`.
- API publique : `/api/marketplace/products`, `/api/marketplace/options`, `/api/marketplace/orders`.

Les comptes existants gardent leur accès administrateur. Les nouvelles inscriptions créent uniquement des vendeurs. Les vendeurs ne peuvent accéder ni à l'ancien backoffice ni aux produits ou commandes des autres vendeurs. Une boutique suspendue disparaît du catalogue et perd son accès de gestion.

## Premier démarrage

1. Déployer le backoffice en premier : le workflow lance la migration additive `2026_11_05_000001_create_marketplace`.
2. Les livres sont copiés dans le catalogue général ; les tables et commandes historiques sont conservées. Le catalogue général devient la source du stock pour les nouvelles ventes. Ne plus utiliser les archives livres pour gérer les produits importés.
3. Dans les paramètres marketplace, renseigner les instructions de paiement à la plateforme et le taux de commission souhaité. Le taux initial est de 0 %, pour ne pas prélever de commission non configurée.
4. Choisir la publication immédiate ou la validation préalable (valeur initiale). Une exception peut être définie pour chaque vendeur.
5. Chaque vendeur renseigne les instructions de paiement de sa boutique pour proposer le paiement direct.
6. Déployer le front. Son `FRONT_API_BASE_URL` doit désigner l'API du backoffice. Les appels sont faits côté serveur ; aucun secret n'est transmis au navigateur.

Le front n'invente aucun produit si l'API est indisponible. Il affiche un état d'erreur et permet de réessayer.

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
