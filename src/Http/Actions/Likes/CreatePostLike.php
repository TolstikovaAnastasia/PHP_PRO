<?php

namespace Anastasia\Blog\Http\Actions\Likes;

use Anastasia\Blog\Exceptions\{HttpException, LikesIsAlreadyExists};
use Anastasia\Blog\Blogs\{LikeToPost, UUID};
use Anastasia\Blog\Http\Actions\ActionInterface;
use Psr\Log\LoggerInterface;
use Anastasia\Blog\Http\{Auth\TokenAuthenticationInterface,
    Request,
    Response,
    SuccessfulResponse,
    ErrorResponse};
use Anastasia\Blog\Repositories\LikesRepo\LikePostRepositoryInterface;
use Anastasia\Blog\Repositories\PostsRepo\PostsRepositoryInterface;

class CreatePostLike implements ActionInterface
{
    public function __construct(
        private LikePostRepositoryInterface $likesRepository,
        private PostsRepositoryInterface $postsRepository,
        private TokenAuthenticationInterface $authentication,
        private LoggerInterface $logger,
    )
    {
    }

    public function handle(Request $request): Response
    {
        try {
            $post_uuid = new UUID($request->jsonBodyField('post_uuid'));
            $post = $this->postsRepository->get($post_uuid);
            $user = $this->authentication->user($request);

            $newLikeUuid = UUID::random();
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $this->likesRepository->likeForPostExists($post_uuid, $user->uuid());
        } catch (LikesIsAlreadyExists $e) {
            return new ErrorResponse($e->getMessage());
        }

        $likePost = new LikeToPost(
            $newLikeUuid,
            $post,
            $user,
        );

        $this->likesRepository->save($likePost);
        $this->logger->info("Post created: $newLikeUuid");

        return new SuccessfulResponse([
            'uuid' => (string)$newLikeUuid,
        ]);
    }
}