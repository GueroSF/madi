<?php

namespace App\Utils;


use App\Entity\Post;
use App\Entity\PostInfo;
use App\Entity\User;
use App\Repository\PostInfoRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class ShowPost
{
    /**
     * @var ManagerRegistry
     */
    private $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    public function markAsRead(User $user, Post $post): void
    {
        /** @var PostInfoRepository $repo */
        $repo = $this->managerRegistry->getRepository(PostInfo::class);

        $info = $repo->findByUserAndPost($user, $post);
        if ($info->getReaderAt() === null) {
            $info->setReaderAt(new \DateTime());
        }

        $this->managerRegistry->getManager()->persist($info);
        $this->managerRegistry->getManager()->flush();
    }
}
