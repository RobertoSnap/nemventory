# Create a secure SAAS application using NEM blockchain.

I’m writing this article to serve as a resource for SAAS projects that need some of its functionality backed on NEM blockchain. This functionality could be:
- Decentralize information
  - You need to use the blockchain as a shared, secure and immutable source for some of your data. 
    - List of trusted servers. 
    - List of members.
    - Master items.
- Documentation
  - You need share some immutable and timestamped information.
    - Certificates
    - Service work
- Assets of value
  - You need to use the blockchain to handle transferable and valuable assets.
    - In-game currencies
    - Forum points
    - Tokens of value
    - Identity
- Transact
  - You need to send or receive payments.
    - Donations
    - E-commerce
- Encrypt
  - You need to encrypt and store some pieces of information.
     - Health records.
     - Secret messages.

## Background 

I’m creating this article based on my experience in building [NEMventory](http://nemventory.nem.today). A simple proof of concept inventory and trade system using mosaics under a centralized namespace. The backend was created with Laravel and frontend with VUE. 

## Off-chain contracts
The challenge with a centralized namespace is giving users limited permission to interact with the namespace, in other words, a contract.  Often referred too as smart “off-chain” contracts. With NEM I can write this code in whatever language a prefer. In NEMventory there is a contract for creating an asset on the centralized namespace. This contract is written in PHP and running in web application backend. The contract gets initiated when a transfer with a certain message is received. As we don’t want to store “large” amount of data on the blockchain, the message will only hold a reference to the information. If the referenced information is sufficient and the transfer has enough XEM, the contract will execute. When contract executes, it creates a mosaic on the namespace based on the referenced information. 

******* PICTURE Off-chain contract

There is another layer of security on this contract. The contract can only purpose transactions. Because the account is multi-sig, a human or a bot will also need to confirm the transactions based on its logic. 

## Infrastructure

Another challenge is how our public SAAS application will communicate with the NEM network.

If we are only requesting open information from the blockchain, we don’t need a secure communication channel. But if our communication “contains” private keys, we will need to secure this connection. Then we have two options:

1.    Sign the data before announcing it to a remote node.
1.    Run a local node that signs and announces the data for you.

For option one, you will either need to create your own [Ed25519](https://ed25519.cr.yp.to/index.html) signing tool, or you can use [NEM-SDK]( https://github.com/QuantumMechanics/NEM-sdk) JavaScript library. I did not try out option 1 for NEMventory as we created a secure backend that has local communication with an NEM node which can sign the transactions for us. Thought we could image a SAAS solution where transactions were only signed by the client. And to enhance it even further, only through client-side decryption with user password. Then we But that is for another article.

In this SAAS typology, we will use option two where we have a local NEM node to communicate with the blockchain network.  This NEM node is called NIS. Installation instructions can be found [here]( https://blog.nem.io/ubuntu-installation-guide-standalone/). It has its API which you can find documentation on [here]( https://nemproject.github.io/). Our web app backend will communicate with the NEM node, which in turn can serve information to our web app frontend through its API. There are several good libraries in multiple languages that simplify the communication with NIS. 

- JavaScript
  - [NEM-sdk](https://github.com/QuantumMechanics/NEM-sdk)
  - [NEM Library](https://nemlibrary.com/)
- PHP
  - [PHP NEM Laravel](https://github.com/evias/php-nem-laravel)
- [See more libraries for other languages…](https://docs.nem.io/en/nis-wrappers)

## Multi-sig

NEM blockchain comes with built-in features like multi-sig authentication which we will use to keep the main account with funds secure in the SAAS infrastructure.
 
The backend web app will only hold one of the multi-sig cosigner keys. So an intruder would only be able to suggest transactions. Then we can put logic in our signing (bot or human) on what transactions should be signed.

If our server got compromised (without a bot on the same server). The intruder does not have enough access to steal funds from the main account.

This secures our valuable assets. We can push it even further by withdrawing assets into cold storage if necessary. 


***** NEM INFRASTRUCTURE PICTURE

## Start your own blockchain SAAS project

So if you want to create a SAAS application that leverages functionality the NEM blockchain provides. 
1.    Setup an ubuntu server
1.    Setup a NEM node on server [here]( https://blog.nem.io/ubuntu-installation-guide-standalone/).
1.    Create a NEM 2-3 multisig account (Tutorial [Part 1](https://vimeo.com/220620269), [Part 2](https://vimeo.com/220620399 ))
4.    Setup your web application. ( [Angular2](https://github.com/guillemsole/nem-library-angular2-seed), [Laravel (..coming)]())
5.    Connect web application to NEM node.

You can find a full SAAS example with [NEMventory here] (https://github.com/RobertoSnap/nemventory). 

### Setup NEMventory example
![NEMventory](http://nemventory.nem.today/images/logo_color.png)

NEMventory consists of a Laravel 5.4 backend and a Vue SPA frontend. To run a secure namespace, you will have to setup a multisig account with at least one public and one private signer. The public signer will live in the .env file on the backend. The private signer can be yourself or you can setup a bot to do it for you.

1. Setup a Laravel enviroment. https://laravel.com/docs/5.4/installation 
   2. Windows: https://laravel.com/docs/5.4/homestead
   2. Mac: https://laravel.com/docs/5.4/valet
1. Clone this repo into a public folder ```git clone https://github.com/RobertoSnap/nemventory.git Nemventory```
1. Run ```composer install```
1. Setup a DB for your Laravel installation and run PHP artisan migrate```
1. Run PHP artisan passport:install```. This will generate the keys for API based authentication.
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
