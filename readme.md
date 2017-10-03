# NEMventory: Trade and inventory system backed on NEM blockchain.

NEMventory is a secured namespace on NEM blockchain. With NEMventory web application you can request the creation of items for an XEM fee. Requested items are distributed back to address of fee-payer. 

NEMventory handles addresses as warehouses. You can create or import addresses to use as warehouses. NEMventory web application supports simple and multisig accounts. The importer will detect the type of account and guide you through the import process. 

On testnet, each newly created account will have a warehouse crated for them. The NEMventory bot will also issue some test xem and starting items to all new accounts (as longs as stocks hold up).

From the NEMventory web application you can:
- Request and create inventory items.
- See inventory information across warehouses
- Create, buy and receive purchase orders.
- Create, sell and send sales orders. 

## Create your secure inventory namespace
NEMventory consists of a Laravel 5.4 backend and a Vue SPA frontend. To run a secure namespace, you will have to setup a multisig account with one public and one private signer. The public signer will live in the .env file on the backend. The private you can choose if you want to sign yourself or setup a bot to do it.

1. Setup a Laravel enviroment. https://laravel.com/docs/5.4/installation 
   2. Windows: https://laravel.com/docs/5.4/homestead
   2. Mac: https://laravel.com/docs/5.4/valet
1. Clone this repo into a public folder ```git clone https://github.com/RobertoSnap/nemventory.git Nemventory```
1. Run ```composer install```
1. Setup a DB for your Laravel installation and run ```php artisan migrate```
1. Run ```php artisan passport:install```. This will generate the keys for API based authentication.
1. Run ```npm install```
1. Run ```npm run watch```
1. Then in your .env file, configure the following:

```PHP MAIN_ACCOUNT_ADDRESS=
   MAIN_ACCOUNT_PUBLIC_KEY=
   MAIN_PUBLIC_ACCOUNT_PUBLIC_KEY=
   MAIN_PUBLIC_ACCOUNT_PRIVATE_KEY=
   NEM_NODE_IP=127.0.0.1
   NEM_NAMESPACE=nemventory
   NEM_ITEM_NAMESPACE=nemventory.items
   NEM_ADDRESS=TD4SAQFGF3DP3IJAXJA2GYGQ3HZVD3AS3UIZ44EA
```

You can sign the transactions with Nanowallet or you setup a bot to do it for you. For NEMventory I'm using this one [Greg Evias Node JS bot](https://github.com/evias/nem-nodejs-bot).

You must setup an NEM node on the server [Ubuntu NEM node turtorial](https://blog.nem.io/ubuntu-installation-guide-standalone/)