<?php

namespace App\Tests\Controller;

use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ConferenceControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Give your feedback');
    }

    public function testCommentSubmission()
    {
        $client = static::createClient();
        $client->request('GET', '/conference/dnepr-2025');

        $client->submitForm('Submit', [
            'comment[author]' => 'Dony',
            'comment[text]' => 'This is a comment',
            'comment[email]' => $email = 'dony@gmail.com',
            'comment[photo]' => dirname(__DIR__, 2).'/public/images/under-construction.gif'
        ]);

        $this->assertResponseRedirects();

        $comment = self::getContainer()->get(CommentRepository::class)->findOneByEmail($email);
        $comment->setState('published');
        self::getContainer()->get(EntityManagerInterface::class)->flush();

        $client->followRedirect();
        $this->assertSelectorExists('div:contains("There are 2 comments")');
    }

    public function testConferencePage()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertCount(2, $crawler->filter('h4'));

        $client->clickLink('View');

        $this->assertPageTitleContains('Conference Guestbook - Dnepr 2025');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Dnepr 2025 Conference');
        $this->assertSelectorExists('div:contains("This is a comment")');
    }
}
