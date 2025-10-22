<?php

namespace App\Tests;

use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminControllerTest extends WebTestCase
{
    public function testReviewCommentAccept(): void
    {
        $client = static::createClient();

        $adminRepository = self::getContainer()->get('doctrine')->getRepository(\App\Entity\Admin::class);
        $admin = $adminRepository->findOneByUsername('admin');
        $client->loginUser($admin);

        $comment = self::getContainer()->get(CommentRepository::class)
            ->findOneBy(['state' => 'ham']);

        $client->request('GET', '/admin/comment/review/' . $comment->getId());

        $this->assertResponseIsSuccessful();

        self::getContainer()->get(EntityManagerInterface::class)->refresh($comment);
        $this->assertSame('published', $comment->getState());
    }

    public function testReviewCommentReject(): void
    {
        $client = static::createClient();

        $adminRepository = self::getContainer()->get('doctrine')->getRepository(\App\Entity\Admin::class);
        $admin = $adminRepository->findOneByUsername('admin');
        $client->loginUser($admin);

        $comment = self::getContainer()->get(CommentRepository::class)
            ->findOneBy(['state' => 'potential_spam']);

        $client->request('GET', '/admin/comment/review/' . $comment->getId() . '?reject=1');

        $this->assertResponseIsSuccessful();

        self::getContainer()->get(EntityManagerInterface::class)->refresh($comment);
        $this->assertSame('rejected', $comment->getState());
    }
}
