<?php
require 'vendor/autoload.php';
use Web3\Web3;

function anchor_hash_on_chain($hash, $chain = 'sepolia') {
    $web3 = new Web3('https://sepolia.infura.io/v3/YOUR_PROJECT_ID');
    // Create and send transaction with hash as data
    // ...
    return $tx_hash;
}
