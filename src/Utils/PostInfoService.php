<?php

namespace App\Utils;


use App\Entity\Post;
use App\Entity\PostInfo;
use App\Entity\User;
use App\Repository\PostInfoRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class PostInfoService
{
    /**
     * @var ManagerRegistry
     */
    private $managerRegistry;

    /**
     * @var PostInfo | null
     */
    private $info = null;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    public function getInfo(): ?PostInfo
    {
        return $this->info;
    }

    public function isSign(): bool
    {
        return $this->getInfo() === null ? true : $this->getInfo()->isSign();
    }

    public function findPostInfo(User $user, Post $post): ?PostInfo
    {
        /** @var PostInfoRepository $repo */
        $repo = $this->managerRegistry->getRepository(PostInfo::class);

        $this->info = $repo->findByUserAndPost($user, $post);

        return $this->getInfo();
    }

    public function markAsRead(): void
    {
        if (!$this->info->isReader()) {
            $this->info->setReaderAt(new \DateTime());
        }

        $this->managerRegistry->getManager()->persist($this->info);
        $this->managerRegistry->getManager()->flush();
    }

    public function markAsSigh(): void
    {
        if (!$this->info->isSign()) {
            $this->info->setSignAt(new \DateTime());
        }

        $this->managerRegistry->getManager()->persist($this->info);
        $this->managerRegistry->getManager()->flush();
    }
}
