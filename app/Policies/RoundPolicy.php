<?php

namespace App\Policies;

use App\Models\Game;
use App\Models\Round;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RoundPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Round  $round
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Round $round)
    {
        return $user->id == $round->author_id;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user, Game $game)
    {
        return $game->locked_at === null
            && (
                $game->status == Game::STATUS_STARTED
                || ( $game->status == Game::STATUS_DRAFT && $game->user_id == $user->id )
            );
    }

    /**
     * @param User $user
     * @param Game $game
     * @return bool
     */
    public function getNext(User $user, Game $game)
    {
        $latestRound = $game->rounds()->latest()->first();
        $gameIsStarted = $game->status == Game::STATUS_STARTED;
        $notAuthorAndGameNotLocked = is_null($game->locked_at) && $latestRound?->author_id != $user->id;
        $authorAndGameLocked = !is_null($game->locked_at) && $latestRound?->author_id == $user->id;
        $isAuthor = $game->user_id == $user->id;
        $gameIsDraftOrAwaitingFirstRound = in_array($game->status, [Game::STATUS_DRAFT, Game::STATUS_WAITING_FIRST_ROUND]);

        return ( $gameIsStarted && ( $notAuthorAndGameNotLocked || $authorAndGameLocked )  )
            ||
            ( $isAuthor &&  $gameIsDraftOrAwaitingFirstRound );

    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Round  $round
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Round $round)
    {
        return $user->id == $round->author_id && $round->status === Round::STATUS_DRAFT;
    }

    /**
     * @param User $user
     * @param Round $round
     * @return bool
     */
    public function publish(User $user, Round $round)
    {
        return $user->id == $round->author_id && $round->status == Round::STATUS_DRAFT;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Round  $round
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Round $round)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Round  $round
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Round $round)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Round  $round
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Round $round)
    {
        //
    }
}
