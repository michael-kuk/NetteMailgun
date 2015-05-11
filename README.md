# NetteMailgun
Nette Framework Nette\Mail\IMailer implementation allowing sending Nette\Mail\Message through Mailgun

## Installation

CLI: composer require mkuk/mailgun-mailer:dev-master

composer.json : require: {"mkuk/mailgun-mailer": "dev-master"}

## Usage

The Mailer class resides in NetteMailgun namespace. It can be instantiated as follows:

    $mailer = new \NetteMailgun\MgMailer('mg.example.com', 'Secret API key')
    
__Constructor parameters__ are
  - Domain - domain you have created in the mailgun service
  - API Key - Your Mailgun API key
  
Use MgMailer by calling __send__ method with \Nette\Mail\Message Object as a parameter.
