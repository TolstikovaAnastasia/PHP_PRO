<?php

namespace Anastasia\Blog\Http\Actions\Likes;

use Anastasia\Blog\Exceptions\{HttpException, LikesIsAlreadyExists};
use Anastasia\Blog\Blogs\{LikeToPost, UUID};
use Anastasia\Blog\Http\Actions\ActionInterface;
use Anastasia\Blog\Http\{Request, Response, SuccessfulResponse, ErrorResponse};
use Anastasia\Blog\Repositories\LikesRepo\LikePostRepositoryInterface;
use Anastasia\Blog\Repositories\PostsRepo\PostsRepositoryInterface;
use Anastasia\Blog\Repositories\UsersRepo\UsersRepositoryInterface;

class CreatePostLike implements ActionInterface
{
    public function __construct(
        private LikePostRepositoryInterface $likesRepository,
        private PostsRepositoryInterface $postsRepository,
        private UsersRepositoryInterface $usersRepository,
    )
    {
    }

    public function handle(Request $request): Response
    {
        try {
            $post_uuid = $request->jsonBodyField('post_uuid');
            $user_uuid = $request->jsonBodyField('user_uuid');

            $newLikeUuid = UUID::random();
            $post = $this->postsRepository->get(new UUID($post_uuid));
            $user = $this->usersRepository->get(new UUID($user_uuid));

            $like = new LikeToPost(
                $newLikeUuid,
                $post,
                $user,
            );

            $this->likesRepository->save($like);

        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $this->likesRepository->likeForPostExists($post_uuid, $user_uuid);
        } catch (LikesIsAlreadyExists $e) {
            return new ErrorResponse($e->getMessage());
        }

        return new SuccessfulResponse([
            'uuid' => (string)$newLikeUuid,
        ]);
    }
}