<?php

/*
 * The MIT License
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace NetteMailgun;

use Nette\Mail\IMailer,
    Nette\Mail\Message,
    Mailgun;


/**
 * Description of MgMailer
 *
 * @author Michael Kuk
 */
class MgMailer implements IMailer{
    /** @var Mailgun\Mailgun */
    private $mg;

    /** @var \string */
    private $domain;

    /**
     * Constructor -> Get settings
     * @param array $config
     */
    public function __construct($domain, $apiKey) {
	if(empty($domain) || empty($apiKey)){
	    throw new \Exception('Invalid Domain and/or API Key parameter');
	}
	$this->domain= $domain;
	$this->mg=new Mailgun\Mailgun($apiKey);
    }

    /**
     * Implementation of Send method
     * ============================
     * 
     * Parse Nette\Mail\Message and send vie Mailgun
     * @param \Nette\Mail\Message $mail
     */
    public function send(Message $mail) {
	$nMail = clone $mail;
	$cFrom = $nMail->getHeader('Return-Path')? : key($nMail->getHeader('From'));
	$to = $this->generateMultiString((array) $nMail->getHeader('To'));
	$cc = $this->generateMultiString((array) $nMail->getHeader('Cc'));
	$bcc = $this->generateMultiString((array) $nMail->getHeader('Bcc'));

	$nMail->setHeader('Bcc', NULL);
	$data = $nMail->generateMessage();
	$cData = preg_replace('#^\.#m', '..', $data);

	return $this->mg->sendMessage($this->domain, array(
	    'from' => $cFrom,
	    'to' => $to,
	    'cc' => $cc,
	    'bcc' => $bcc,
	), $cData);
    }

    /**
     * Generate single/multiple recipient strings
     * @param array $mails
     * @return string
     */
    private function generateMultiString($mails) {
	$return = '';
	$c = 0;
	foreach ($mails as $k => $v) {
	    $return .= $c === 0 ? '' : ', ';
	    $return.= (empty($v) ? '' : $v . ' ') . "<$k>";
	    $c++;
	}
	return $return;
    }
}