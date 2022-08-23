<?php

namespace Anastasia\Blog\Http\Actions\Comments;

use Anastasia\Blog\Blogs\{UUID ,Comment};
use Anastasia\Blog\Exceptions\{HttpException, InvalidArgumentException, PostNotFoundException, UserNotFoundException};
use Anastasia\Blog\Http\Actions\ActionInterface;
use Anastasia\Blog\Http\ErrorResponse;
use Anastasia\Blog\Http\{Request, Response, SuccessfulResponse};
use Anastasia\Blog\Repositories\CommentsRepo\CommentsRepositoryInterface;
use Anastasia\Blog\Repositories\PostsRepo\PostsRepositoryInterface;
use Anastasia\Blog\Repositories\UsersRepo\UsersRepositoryInterface;

class CreateComment implements ActionInterface
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository,
        private PostsRepositoryInterface $postsRepository,
        private CommentsRepositoryInterface $commentsRepository,
    )
    {
    }

    public function handle(Request $request): Response
    {
        try {
            $authorUuid = new UUID($request->jsonBodyField('author_uuid'));
            $user = $this->usersRepository->get($authorUuid);

            $postUuid = new UUID($request->jsonBodyField('post_uuid'));
            $post = $this->postsRepository->get($postUuid);

            $newCommentUuid = UUID::random();

            $comment = new Comment(
                $newCommentUuid,
                $user,
                $post,
                $request->jsonBodyField('text'),
            );

            $this->commentsRepository->save($comment);

        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        return new SuccessfulResponse([
            'uuid' => (string)$newCommentUuid,
        ]);
    }
}