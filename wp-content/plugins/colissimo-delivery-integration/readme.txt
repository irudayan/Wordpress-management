===Colissimo Delivery Integration === 
Contributors: Halyra
Tags: woocommerce, colissimo, shipping, laposte, parcel, logistic, tracking
Requires at least:  5.2
Tested up to: 5.7.1
Requires PHP: 7.0
Stable tag: trunk
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Une intégration complète de Colissimo dans WooCommerce.


== Description ==

> #### Le plugin [CDI - Collect and Deliver Interface](https://wordpress.org/plugins/collect-and-deliver-interface-for-woocommerce/) version multi-transporteurs, est disponible et remplace ce plugin.
>
> Il comporte actuellement 4 transporteurs : Colissimo, Mondial Relay, UPS, Click&Collect
>
> [CDI - Collect and Deliver Interface](https://wordpress.org/plugins/collect-and-deliver-interface-for-woocommerce/) assure la migration et la continuité de fonctionnement avec votre plugin actuel.
>
> #### C'est désormais le plugin de référence de CDI qui seul porte les évolutions. 
>
> <a href="http://cdiwoo.com/cdi-guide-migration.png" rel="noopener" target="_blank">Guide Migration</a>


= CDI et CDI+ : =

Les fonctions de Colissimo Delivery Integration se répartissent en 2 niveaux de service :

* Un service de base en utilisation libre appelé CDI,
* Un service CDI+ qui nécessite un enregistrement des utilisateurs, pour des fonctions additionnelles à plus forte valeur, avec une assistance support et conseil :

 - <a href="http://colissimodeliveryintegration.com/Compare-CDI-CDIplus.pdf" rel="noopener" target="_blank">Comparaison CDI Service de base vs CDI + </a> 

 - Le PARCOURS COLIS plus bas dans cette page, précise l'utilisation du plugin.

 - <a href="http://colissimodeliveryintegration.com/cdi-structure.png" rel="noopener" target="_blank">Structure de CDI</a> 

Colissimo Delivery Integration est une initiative indépendante, sans lien d’intérêt avec La Poste ou le service Colissimo de La Poste. Contrairement à CDI qui est un logiciel libre, CDI+ est un produit commercial réservé à ses seuls utilisateurs enregistrés. Depuis 2016, CDI accompagne les entreprises avec ses prestations sur le marché de la connexion des installations Woocommerce aux Web Services Colissimo-La Poste. 

= Fonctions : =

CDI+ réalise l'intégration complète des services Colissimo (groupe La Poste) dans WooCommerce. CDI+ permet:

* L’utilisation d’une méthode de livraison Woommerce puissante et bien adaptée à Colissimo : sélection des tarifs par classes de produit, fourchette de prix du panier HT ou TTC, fourchette de poids des articles ; tarifs variables programmables ; gestion des coupons promo Woocommerce ; mode inclusion/exclusion pour les classes produit et les coupons promo ; macro-classes pour sélections complexes ; gestion de tarifs prioritaires ; places de marché.
* Au client son choix de méthode de livraison. Ses données de suivi colis figurent dans les courriels et dans ses vues Woocommerce de commandes. Il dispose d’une extension de l’adresse basique Woocommerce (2 lignes) aux standards postaux 4 lignes.
* La gestion de toutes les options Colissimo: signature, assurance complémentaire, expéditions internationales, type de retour, caractéristiques CN23, … . Le service des retours de colis par le client avec pilotage/autorisation par l’Administrateur. Le traitement des points relais avec carte Google Maps.
* La gestion des colis des commandes produites par Woocommerce dans une passerelle dédiée, asynchrone du flux Woocommerce, avec 3 modes de traitement possibles :
  - Mode manuel par l’export d’un csv permettant ensuite à l’Administrateur l’automatisation de scripts pour le logiciel du transporteur qu’il utilise ;
  - Mode automatique qui exécute en ligne le Web service Colissimo affranchissement de Colissimo pour récupérer automatiquement les étiquettes et autres données émises par Colissimo ;
  - Mode personnalisé qui active un filtre Wordpress pour passer les données des colis à une application propre au gestionnaire du site, lui permettant ainsi de s’adapter au protocole de son transporteur (qui peut être différent de Colissimo).
* Une gestion automatisée au maximum des colis dans la passerelle : soumission en 1 clic des colis au transporteur, purge automatique des colis traités, vue directe des étiquettes et cn23, impression globale des étiquettes, export global des colis, historique des colis, outil de debug des anomalies d'exploitation.
* Un suivi temps réel dans la console d’administration des commandes, de la situation de délivrance des colis expédiés.
* Une utilisation des grandes fonctions à la carte du gestionnaire du site selon son besoin, et si nécessaire un paramétrage des codes produit Colissimo et des pays admis aux offres du transporteur.

La répartition actuelle des fonctions entre CDI et CDI+ reste susceptible d'évolutions.


== Installation ==

1. Installer le plugin de façon traditionnelle, et l’activer.
1. Aller dans la page  : Woocommerce -> Réglages -> Colissimo, et adapter quand nécessaire les réglages des 8 onglets : Réglages généraux, CN23, Interface client, Mode automatique, Références aux livraisons, Méthode Colissimo, Retour colis, Impression d'étiquettes. Bien veiller à enregistrer un à un chacun des onglets. Les réglages par défaut de CDI permettent déjà un fonctionnement immédiat.
1. Renseigner vos réglages de l'identifiant et du mot de passe Colissimo, ainsi que de la clé Google Maps, si vous utilisez ces fonctions.
1. Aller dans la page Woocommerce des commandes et cliquer sur l’icône «colis ouvert» d’une commande pour créer dans la passerelle CDI un colis à expédier. Si besoin avant cela, les caractéristique du colis peuvent être visualisées et modifiées en ouvrant la commande et en modifiant sa zone «Métabox CDI».
1. Aller dans la  passerelle CDI  Woocommerce -> Colissimo , pour retrouver les colis à expédier.
1. Cliquer sur le bouton «Automatique» pour soumettre au serveur Colissimo tous les colis en attente, ce qui retournera les étiquettes d’affranchissement.

= Activation de CDI+ =

1. Le plugin est le même pour le service de base CDI et CDI+, les droits étant affectés dynamiquement.
1. Dans un premier temps, installer le plugin CDI depuis Wordpress ce qui donnera accès au service de base CDI.
1. Ensuite, s'abonner à CDI+ depuis les réglages du plugin CDI ou de la passerelle CDI.

= Assistance et support : =

* Le support du service de base CDI est assuré par les participants au forum wordpress.org . L’auteur ne fournit pas de support sur le forum wordpress.org .
* Seuls les utilisateurs du service CDI+ disposent d’un support spécifique et personnalisé


== Frequently Asked Questions ==

= Où puis-je obtenir de l'aide ou  parler à d'autres utilisateurs ?  =

* Le support gratuit est disponible à l'aide de la communauté dans le [Forum de Colissimo Delivery Intégration](https://wordpress.org/support/plugin/colissimo-delivery-integration).
C'est le meilleur moyen parce que toute la communauté profite des solutions données dans le forum. Vous pouvez utiliser indifféremment l'anglais ou le français. 
Si vous étés enregistré à CDI+ , vous bénéficiez en plus d’un support premium pour vous assister. 

= Puis-je obtenir une personnalisation poussée de CDI ? =

Une personnalisation de base peut être obtenue par les paramètres CDI. Mais vous pouvez allez plus loin et avoir une personnalisation beaucoup plus fine lorsque vous utilisez des filtres Wordpress installés dans les fichiers  CDI. La règle d'utilisation de chacun de ces filtres est donnée par son utilisation dans les fichiers php activant ces filtres. Des exemples d'utilisation sont donnés dans le fichier includes/WC-filter-examples.php.

= En mode automatique (service web), quelle est la règle d'affectation du code de produit Colissimo ? =

* Le code produit affiché dans la Metabox Colissimo de la commande a toujours la priorité. Il peut être forcé manuellement dans la Metabox Colissimo. Si le code produit n'existe pas dans la Metabox Colissimo, le mode automatique utilisera les paramètres par défaut définis dans Woocommerce-> Réglages-> Colissimo (France, Outre-mer, Europe, International)
* Le code produit inséré dans la Metabox Colissimo  peut être initialisé lors de l'analyse d'une méthode d'expédition: soit il correspond à une méthode d'expédition définie dans Woocommerce-> Réglages-> expédition-> Colissimo; soit un code produit est retourné dynamiquement par Colissimo "Point de livraison - Pickup locations" service web.

= Où sont les panneaux de réglages/controle de CDI ? =

* Les panneaux sont en 3 endroits:
   -Dans Woocommerce-> Réglages-> Colissimo pour les réglages généraux, cn23, infos clients, mode automatique, références aux livraisons, méthode Colissimo, retour colis, impression d'étiquettes
   -Dans Woocommerce-> Réglages-> Expédition et dans chaque instance en zone d'expédition pour les paramètres relatifs à une méthode d'expédition Colissimo.
   -Dans Woocommerce-> Colissimo pour contrôler la passerelle pour la production d'étiquettes de colis et la gestion des colis.

= Comment effectuer des tests et se mettre en mode log/debug ? =

* Configurez votre fichier wp-config.php en mode débogage en insérant, à la place de la ligne "define ('WP_DEBUG', false);" , ces 3 lignes : define ('WP_DEBUG', true); Define ('WP_DEBUG_LOG', true); Define ('WP_DEBUG_DISPLAY', false); 
* Activez le paramètre "log for debugging purpose" dans les paramètres de CDI, et choississez les modules pour lesquels vous voulez un log. Après exécution de votre séquence à tester, affichez le fichier wp-content/debug.log pour voir les traces. Si vous afficher votre fichier debug.log dans le forum: a) supprimez le fichier debug.log avant votre test pour réduire sa longueur b) pour votre sécurité supprimez dans votre fichier toutes vos données contractNumber et password.
* N'oubliez pas lorsque votre site passe de test en exploitation de restaurer votre fichier wp-config.php avec "define ('WP_DEBUG', false);".

= Puis-je utiliser un transporteur différent de Colissimo ? =

* Non pour le mode automatique (Web Service). Ainsi que pour les fonctions spécifiques comme point de retrait.
* Oui, c'est une possibilité pour le mode manuel et le mode personnalisé, pour la méthode de livraison, et pour les infos rendues au client. Toutefois, les données passées dans la passerelle respecteront les normes Colissimo (signature, montant du remboursement, ...).

= A quoi sert le paramètre "Nettoyage automatique des colis dans la passerelle Colissimo" des paramètres généraux? =

* La Metabox Colissimo des commandes WooCommerce sont automatiquement mises à jour des code de suivi et autres informations depuis la passerelle. Le commutateur d'état est positionner à "Dans le camion" dans la Metabox. Cependant, les paquets Colissimo présents dans la passerelle sont supprimés automatiquement après ces opérations, uniquement si le paramètre "Nettoyage automatique des colis dans la passerelle Colissimo" est activé. C’est le mode de fonctionnement recommandé.
* Ne pas positionner ce paramètre peut être toutefois utile lorsque l'administrateur souhaite gérer manuellement les colis par des sessions à sa main; Le nettoyage de la passerelle doit alors être effectué manuellement.


== Screenshots == 

1. Parameters in Woocommerce settings panel.
1. Orders page of Woocommerce.
1. Colissimo box in order details panel.
1. Colissimo gateway panel.
1. Colissimo shipping settings (shipping methods).
1. Colissimo shipping settings (Points de livraison - Pickup location).
1. Checkout page with Pickup locations list.
1. Checkout page, zoom on a locations.


== Changelog == 


= 3.7.18 (2021-05-10) =
* Add Encrypted files for cdistore (option in settings)

= 3.7.16 (2021-04-30) =
* Fix Security for cdistore directory

= 3.7.15 (2021-03-16) =
* Fix New cdi data transfer mecanism beetwen frontend and backend
* Some typo and Css fix

= 3.7.14 (2020-12-09) =
* New Button "Connect" when press forced a CDI server call
* Fix Add cache in test Colissimo server to avoid its overload
* Fix OpenMap with Wordpress 5.6
* Some typo and Css fix

= 3.7.13 (2020-11-08) =
* Tweak Remove unweighted products from CN23 and logistics documents (especially virtual products)
* Fix Duplicate functions declare plugin_row_meta(), new_blog()
* Add Countries list giving no choice for return of undelivered parcels, in resettable settings (red settings)
* Tweak Check others plugins dependencies and conflicts
* Some typo and Css fix

= 3.7.12 (2020-09-06) =
* Tweak Improved resilience to temporarily internet network or user website incidents
* Tweak Adjust deprecated WP and WC codes
* Add Check that WC address has not been erased during Checkout process
* Some typo and Css fix

= 3.7.11 (2020-08-15) =
* Fix Default start rank of print label
* Fix Jquery defining map position in checkout page not working as expected for some browsers (Safari recent versions)  
* Add Check others plugins dependencies and conflicts
* Some typo and fix

= 3.7.10 (2020-04-27) =
* Add Setting max cn23 articles 
* Fix Check first position setting in printing of address labels
* Fix Add warning message in admin and log when LaPoste Web services is out of service
* Some typo and fix

= 3.7.9 (2020-03-01) =
* Fix Shipping zone CDI method when apostrophe in name of product class
* Tweak Visual pickup display in CDI Open map
* Add Setting max items in logistics documents
* Add Logistics document giving overall the Colissimo delivery status of parcels
* Some typo and fix

= 3.7.8 (2020-02-12) =
* Add More compatibility with orders renumbering plugins
* Fix Gateway modify function of order shipping address
* Add "Indice de localisation" in map pickup détails (when exists)
* Add Parcel ref settings (order id vs order number)  
* Add Additional examples of filters
* Tweak Rework of CDI Open Map : Update to Open layers V6.2.0, Suppress Bootstrap
* Tweak Suppress Ajax condition in ckeckout (no more WC filter rebound)
* Some typo and fix

= 3.7.7 (2019-11-17) =
* Add Filter for bootstrap load : cdi_filterstyle_bootstrap and cdi_filterjs_bootstrap
* Fix Rework shipping method and products selected in the WC multi-packages context
* Add Subscription compatibility (i.e. WC subscriptions )
* Tweak Defaut multi-package is first (was whole cart before)
* Some typo and fix

= 3.7.5 (2019-09-15) =
* Ajust Some titles and default settings 
* Add Multi-parcels with "multi-order-for-woocommerce" plugin full compatibility  
* Add Reset CDI Metabox button (Create a fresh Metabox when a modified order)
* Fix Preload WS Colissimo wsdl in local, to avoid a 'SOAP-ERROR Premature end of data' fatal error (in case WS loading is slow) 
* Some typo and fix

= 3.7.4 (2019-08-16) =
* Fix Workaround for WC()->session->get('chosen_shipping_methods') not fully updating
* Add Siret in membership form
* Some typo and fix

= 3.7.1 (2019-07-11) =
* Fix synchro from Gateway - Refresh of CDI Metabox

= 3.7.0 (2019-07-10) =
* Tweak Rework in "WC-colissimo-choix-livraison"
* Fix Loss of pickup selection in checkout
* Fix Hook callback for woocommerce_review_order_after_cart_contents
* Add in CDI Metabox : synchro from Gateway + Refresh of CDI Metabox
* Some typo and fix

= 3.6.7 (2019-06-18) =
* Tweak Refining the shipping change trigger in shipping checkout
* Tweak Refining the analysis of pickup tariffs statement 
* Fix Loss of pickup selection for some payment reject configurations
* Add Option to not show pickup tariffs to customers (in cart and checkout) when offline detected (outgoing IP, LaPoste, CDI)
* Some typo and fix

= 3.6.6 (2019-05-05) =
* Fix Parcels sort in CDI Gateway
* Fix Undefined offset when legacy shipping method used
* Fix Cn23 label not correctly reset
* Some typo and fix

= 3.6.5 (2019-04-22) =
* Fix Check CDI Metabox exist when creating parcel in CDI gateway
* Tweak After WC orderlist action, stay on same screen
* Tweak Refine click on map  without HTML5 for some themes and/or terminals
* Add Gateway button in order CDI Metabox
* Add Filter for $array_for_carrier before calling Colissimo Web Service
* Add Filter to custom order reference passed to carrier 
* Add Filter to insert instruction for carrier
* Some typo and fix

= 3.6.4 (2019-03-23) =
* Fix Option for "Les armées françaises (S1)" country

= 3.6.3 (2019-03-23) =
* Add Option for "Les armées françaises (S1)" country
* Add Option to include EORI (Economic Operator Registration and Identification) in cn23
* Some typo and fix

= 3.6.2 (2019-03-06) =
* Fix Product name in Metabox CDI not get when product has been suppressed
* Fix Unit for compensation_amount (insurance)
* Some typo and fix

= 3.6.1 (2019-02-19) =
* Fix Mandatory phone number for unregistered customers 

= 3.6.0 (2019-02-16) =
* Fix Open debug.log file 
* Add Order notification in WC order list, that a return label has been asked/printed by the customer 
* Tweak Deposit days limited beetween 1 to 10 
* Tweak Tracking codes limited to uppercase alphanum
* Tweak Default map engine is now "CDI Open maps"
* Add logistics document management (Bordereau de dépôt, Bon de transport, Listes de préparation) Beta
* Add List of shipping methods requiring a mandatory phone number, in settings
* Add Cn23 country codes and post codes EU exemptions in settings
* Some typo and fix

= 3.5.1 (2019-01-16) =
* Add Extend 4 lines postal standart address option in WC shipping packages
* Fix Bordereau button color switch
* Fix Lost of pickupid in session data for some configurations (input error folowed by refresh sequences)
* Tweak Add last error in CDI+ non connected button
* Add Red Colissimo settings defining countries allowed to no-sign parcel reception (Belgium and Switzerland added in list) 
* Some typo and fix

= 3.5.0 (2019-01-05) =
* Fix OM map css similar to GM map css
* Add OM automatic map fallback research when customer line1 address is incorrect
* Add Automated operating sequences (insert parcel in gateway, shipping methods selected for gateway, gateway auto clean, orders "completed" when parcel "intruck")
* Some typo and fix

= 3.4.0 (2018-12-16) =
* Fix Warning non-numeric cn23 weight in Retour colis
* Fix Sanitize customer phone number before WS call
* Fix Sanitize pickup address display on click on the map
* Tweak Test label pdf (and no more obsolete label url) in Retour colis  
* Add Filter 'cdi_filterbool_activate_shipping_rate' in CDI shipping method for custom select of tariffs
* Add Choice between Google Maps or open map (Open Layers, Open Street Map, and Nominatim)
* Some typo and fix

= 3.3.5 (2018-12-01) =
* Tweak Check, display and block inconsistency between product code and location pickup id
* Tweak Some rework in "WC-colissimo-choix-livraison"
* Tweak Some rework in Gateway
* Add New WS "Bordereau de remise" function in gateway
* Fix cdi_get_items_chosen when WPML translation (see example filter in CDI-filters-example.php) 
* Fix Click on GM option
* Some typo and fix

= 3.3.3 (2018-11-17) =
* Fix Include cn23 option for labels setting 
* Fix No show for empty bulk label and cn23 
* Fix Cn23 total shipping for admin orders
* Fix Total net weight for admin orders
* Tweak CDI metabox showed only after admin orders 
* Fix Sanitize export csv (Manuel mode)
* Fix Insurance value forced to 0 for Colissimo Europe zone
* Some typo and fix

= 3.3.2 (2018-10-30) =
* Fix 3.3.0 svn update (3.3.2 idem 3.3.0)

= 3.3.0 (2018-10-30) =
* Fix Cn23 data for variable products in CDI Metabox and Gateway
* Tweak Test if product when calculating total net weight
* Tweak Sanitize pickup names
* Tweak Rebuild WS Client library (WS Affranchissement + WS Points de livraison)
* Some typo and fix

= 3.2.20 (2018-10-14) =
* Fix WS error code not correctly tested (fix on version 3.2.19)

= 3.2.19 (2018-10-14) =
* Fix Cart including variable products not correctly computed
* Fix WS error code not correctly tested

= 3.2.18 (2018-09-25) =
* CDI+ : Fix for WS Colissimo Point Livraison (Colissimo-LaPoste WS 09/25 update)
* WARNING : no Web Service compatibility for non CDI+ installations

= 3.2.17 (2018-08-30) =
* Tweak Check and round parcel weight 
* Fix Warning non-numeric value cn23_shipping
* Tweak Colissimo-LaPoste supervision test (curl or file_get_contents)

= 3.2.16 (2018-08-09) =
* Fix Monitoring test that Colissimo-LaPoste is operational (Supervision url)
* Tweak Update last connection time 
* Some typo and fix

= 3.2.15 (2018-08-08) =
* Tweak Add option to choose priority curl VS File_get_contents (for improved stability)
* Fix Products list in CDI Metabox
* Some typo and fix

= 3.2.14 (2018-07-26) =
* Fix Prevent shipping method conflicts with other shipping methods
* Tweak Reworking in shipping method 
* CDI+ : Add option for calculating rates based on the discount price
* CDI+ : Tweak Explanation the 10 minutes time cycle to obtain credentials from CDI
* Some typo and fix

= 3.2.13 (2018-07-06) =
* Add Shipping label in emails and order view
* Add Products list of this shipping in CDI Metabox
* CDI+ : Add WC Multi shipping packages compatibility (i.g. Market places mecanism)
* CDI+ : Add cart or sub-packages choice for weight, price, classes selections in CDI shipping method
* CDI+ : Add choice for cart or a sub-package to ship throught CDI gateway 
* Some typo and fix

= 3.2.12 (2018-06-13) =
* Fix Warning: Invalid argument ... shipping-zone
* CDI+ : Add filters for automatic insertion of tariffs
* Some typo and fix

= 3.2.11 (2018-06-11) =
* CDI+ : Tweak refining GDPR data exported
* CDI+ : Add Macros-shipping-classes in Colissimo shipping method - boolean expressions for complex shipping classes selections
* Some typo and fix

= 3.2.10 (2018-05-25) =
* Tweak Contract settings compatibility
* Tweak Pluging initialization notice annoucing that functions change between CDI and CDI +
* Fix Correct blink function js
* Fix Error cannot redeclare cdi_load_file_get_contents() in some situation
* CDI+ : Add Parcels personnal data in WC GDPR export (EU regulation 2016/679 applying from May 25)
* CDI+ : Choosing the tracking information location in emails
* Some typo and fix

= 3.2.9 (2018-05-21) =
* Fix Debug & Contract settings compatibility
* CDI+ : Tweak Support request tool located in Gateway, Settings and Console
* Some typo and fix

= 3.2.8 (2018-05-19) =
* Tweak Control pattern on CDI+ contract id
* Tweak Simplify debug ticking in settings
* CDI+ : Add Debug tool in Gateway
* Some typo and fix

= 3.2.7 (2018-05-10) =
* Fix Notice M01 M02 when installing a new CDI version
* Tweak Warning SoapClient not installed before fatal error
* Some typo and fix

= 3.2.6 (2018-05-09) =
* Tweak Dynamic config adaptation : curl vs allow_url_fopen
* Tweak Technical structuring
* Tweak Detection of technical inconsistencies in settings
* Tweak Uninstall procedure refinement
* Some typo and fix

= 3.2.4 (2018-04-25) =
* Fix Correct HS tariff url
* Fix Suppress Dpl and Zpl formats in settings
* Fix virtual product compatibility in shipping method
* Some typo and fix

= 3.2.3 (2018-04-06) =
* Tweak Suppress screen_icon() warning
* Tweak Pdf-A4 format forced for all Return labels; more adapted for consumers
* CDI+ : Fix Sanitize Shipping address shown in gateway ckeck address box 
* CDI+ : Fix label Bulk printing in Gateway 
* CDI+ : Fix format Pdf-10x15 Bulk printing in Gateway 
* Some typo and fix

= 3.2.2 (2018-04-04) =
* Tweak Avoid woocommerce crashing when WooCommerce PDF Invoices & Packing Slips used
* CDI+ : Fix label Bulk printing in Gateway (keep A4 and rotate pdf)
* CDI+ : Add Option to select pickup by click on map
* CDI+ : Add Option to choose map location in checkout page

= 3.2.1 (2018-03-29) =
* Fix Fatal error WC-Frontend-Colissimo

= 3.2.0 (2018-03-29) =
* Fix Warning non-numeric value for product without weight
* Fix Content-Disposition and Content-Type for pdf
* CDI+ : Fix Content-Disposition and Content-Type for bulk pdf
* CDI+ : Add empty parcel weight option in shipping method 
* CDI+ : Add full Z10-011 standard (4 lines address) in Woocommerce
* CDI+ : Add Order preview in gateway
* CDI+ : Add CDI Metabox preview in gateway
* CDI+ : Add LaPoste address check/update in gateway
* Some typo and fix

= 3.1.0 (2018-03-11) =
* Fix Auto protect if FPDF class already exist
* CDI+ : Typo and fix 

= 3.0.0 (2018-03-06) =
* Tweak Optimizing  secondary key lenght in cdi table
* Add Ftd (Outre-mer) parameter
* Add Return-receipt parameter
* Add Order private status capacity to update CDI Metabox from gateway (new filter)
* Tweak Control pattern on sender zipcode
* Add Option setting to refresh GMap in checkout
* Tweak Checkout idle status change from 20s to 300s
* Add More settings available for string translation
* Add Examples for insurances
* Add Example for order private status
* Add Order customer message in gateway outputs
* Fix Css adaptation with WC 3.3.1
* Fix Shipping icon display in cart and checkout
* CDI+ : Start of the new option 
* CDI+ : Premium support for CDI+ registered users
* CDI+ : Colissimo shipping methods can be selected or excluded with WC Promo codes 
* CDI+ : Customizable extended Termid list for Colissimo shipping method
* CDI+ : Gateway view of attached pdf label and cn23 (and no more from url)
* CDI+ : Bulk printing of label and CN23 in process in the gateway
* CDI+ : Export csv of parcels in process in gateway
* CDI+ : Export csv history of parcels already send 
* CDI+ : Online postal tracking in CDI Metabox
* CDI+ : Extend WC customer address to 4 lines postal standarts (beta)
* Some typo and fix


== Parcours Colis ==

Le cheminement du colis dans CDI+ donné ici est un exemple du processus, destiné à illustrer les étapes successives dans l’ordre chronologique. Différentes options de réglages ou hooks permettent de personnaliser ce parcours.

= L’achat par les clients Internautes : =

* Chaque commande client suit ce parcours :
-Choix d’un ou plusieurs produits ;
-Sélection du mode de livraison parmi ceux proposés par le e-marchand ;
-Sélection éventuelle du point relais de livraison (personnalisable);

* Réception par le client après chaque commande du mail WC d’accusé comprenant les premières informations de livraison ;

= Contrôle et prise en charge des commandes client : =

* Une fois par jour, ou plus selon l’organisation, dans la page commandes de WC :
-Contrôle des commandes – stocks, incidents, adresses, paiement, etc – dans la liste des commandes WC ;
-Un pictogramme «Carton ouvert – En attente d’expédition» symbolise la préparation du colis qui est en cours ;

* La Metabox CDI dans chaque commande :
-Elle contient les données caractérisant l’expédition qui sera effectuée, la plupart ayant été automatiquement déduites de la méthode d'expédition et des réglages dans CDI ; 
-Contrôle et modification éventuelle des données de la Metabox CDI de chaque commande ; 

* Le click sur le «Carton ouvert» quand sa préparation est terminée change ce pictogramme en un «Chariot de colis (diable) – Déposé dans la passerelle», et créé une entité colis dans la passerelle CDI ;

= Gestion des colis en passerelle d’expédition CDI : =

* La Passerelle CDI se veut l’image d'un quai d’expédition. Une fois par jour, ou plus selon l’organisation :
-Vérification et correction éventuelle des adresses d’expédition (vérification en ligne avec La Poste) ;
-Isolation/blocage pour attente des colis dont le colisage est incomplet ou ayant un problème à régler avant expédition ;

* Click sur «Automatique - Web Service d’Affranchissement» pour annoncer l'ensemble des colis à La Poste ;  
-Il en résulte un remplissage automatique de toutes les zones données colis dans la passerelle ;

* Impressions individuelles ou en lot des étiquettes d’affranchissement, des cn23 et du journal d’expédition ;

* Fermeture des colis, collage des étiquettes d’affranchissement, et dépôt des colis à La poste ;

= Transport et dépôt des colis au Bureau de Poste : =

* Il faut bien un petit quelque chose non dématérialisable !

= Terminaison des commandes : =

* Automatiquement, au retour dans la page commandes de WC :
-Les commandes et les Métabox CDI sont mises à jour des informations d’annonce des colis ; le pictogramme CDI est passé dans un état «Dans le camion»
-Les colis traités dans la passerelle sont supprimés de la passerelle pour ne pas les traiter une 2ème fois ;
-Pour les clients, les vues commande sur le site du e-marchand comprennent désormais les données d’expédition (code suivi, adresse point relais, …) ;
 
* Au click sur le bouton «Terminer» de la commande WC, le mail de fin de commande de WC est envoyé ; il comprend en plus les données d’expédition (code suivi, adresse point relais, …) ;

= Retour du colis par le client : =

* Les conditions de retour sont fixées au cas par cas ou globalement par l’Admin :
-Adaptation éventuelle par l’Admin du paramètre retour colis qui figure dans la Métabox de la commande ;
-Le client se connecte au site du e-marchand, et seulement quand il y est autorisé, dans sa vue commande il lui est proposé une procédure de retour : production, stockage, puis impression de l’étiquette d’affranchissement pour le retour du colis ;
-Le client colle son étiquette sur le colis et le dépose à son Bureau de Poste ;
