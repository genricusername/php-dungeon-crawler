<?php

$playerHP = 20;
$goblinHP = 10;
$zombieHP = 13;
$mimicHP = 11;
$room = 0;
$enemy = 0;

//give them an intro and swap them through the rooms when things get updated
function startGame() {
    global $playerHP, $room;

    echo "You are an adventurer tasked with exploring an unexplored dungeon with the promise of keeping any loot you find.\n";
    echo "You step into the ancient dungeon...\n";

    while (true) {
        switch ($room) {
            case 0:
                roomZero();
                break;
            case 1:
                roomOne();
                break;
            case 2:
                roomTwo();
                break;
            case 3:
                finalRoom();
                return;
        }
    }
}

//entry room, if they go left they get an empty hall and continue forward, if they go right they hit a tripwire but continue forward anyways, if they go forward they get challenged by a random goblin
function roomZero() {
    global $room, $enemy;

    echo "You see 3 paths: Left, Right, or Forward. Which do you take? ";
    $choice = strtolower(trim(readline()));

    if ($choice == "left") {
        echo "You walk down a quiet, empty hallway...\n";
        $room = 1;
    } elseif ($choice == "right") {
        echo "You hit a tripwire! Arrows fly from the walls.\n";
        adjustHP(-3);
        $room = 1;
    } elseif ($choice == "forward") {
        echo "A goblin jumps out with a dagger!\n";
        $enemy = 1;
        combat();
        $room = 1;
    }
}
//if left take them to the room they have to convince a magic guard to left them pass, if they go right enter mimic room and see if they open it combat, if they go forward they get the door puzzle
function roomOne() {
    global $room, $enemy;

    echo "You arrive at another split in the path: Left, Right, or Forward? ";
    $choice = strtolower(trim(readline()));

    if ($choice == "left") {
        guardRoom();
    } elseif ($choice == "right") {
        echo "A chest lies in the middle of the room. Open it? (yes/no): ";
        $check = strtolower(trim(readline()));
        if ($check == "yes") {
            $enemy = 3;
            combat();
            $room = 2;
        } else {
            echo "You walk past the chest.\n";
            $room = 2;
        }
    } elseif ($choice == "forward") {
        puzzleRoom();
    }
}

//if go left zombie combat, if right 50/50 chance to find button, forward they fall into a spike trap if they live they walk back if they die death()
function roomTwo() {
    global $room, $enemy;

    echo "Almost there... Three final paths. Left, Right, or Forward? ";
    $choice = strtolower(trim(readline()));

    if ($choice == "left") {
        echo "A zombie crawls out of the shadows!\n";
        $enemy = 2;
        combat();
        $room = 3;
    } elseif ($choice == "right") {
        echo "An empty room... Look for a button? (yes/no): ";
        $look = strtolower(trim(readline()));
        if ($look == "yes") {
            if (rand(1, 2) === 1) {
                echo "A hidden door opens!\n";
                $room = 3;
            } else {
                echo "No luck, you head back.\n";
                roomTwo();
            }
        }
    } elseif ($choice == "forward") {
        echo "You fall into a spike pit!\n";
        adjustHP(-10);
        if ($GLOBALS['playerHP'] > 0) {
            echo "You survive and climb out. Heading back...\n";
            roomTwo();
        }
        else{
            death();
        }
    }
}
//they win ask if they wanna play again or not
function finalRoom() {
    echo "You've made it to the treasure chamber! You win!\n";
    echo "Would you like to play again? (yes/no): ";
    $choice = strtolower(trim(readline()));
    if ($choice === "yes") {
        resetGame();
        startGame();
    } else {
        echo "Thanks for playing, adventurer!\n";
    }
}

//take a shot to convince the guard to let you by else return to previous room
function guardRoom() {
    global $room;
    echo "A magical guard blocks the way. Try to persuade him? (yes/no): ";
    $choice = strtolower(trim(readline()));
    if ($choice === "yes") {
        if (rand(1, 20) >= 14) {
            echo "You charm your way through.\n";
            $room = 2;
        } else {
            echo "The guard denies you. You retreat.\n";
            roomOne();
        }
    } else {
        echo "You turn back.\n";
        roomOne();
    }
}

//make them bush the button to spell door until they get it right if they fail they get hit by arrow traps
function puzzleRoom() {
    global $room;

    echo "Ancient ruins show a word: D...o..._...R. Buttons: square, circle, triangle.\n";
    echo "Choose a button: ";
    $pick = strtolower(trim(readline()));

    if ($pick == "circle") {
        echo "A door opens.\n";
        $room = 2;
    } else {
        adjustHP(-8);
        echo "Traps go off! Arrows hit you.\n";
        puzzleRoom();
    }
}

//based off the enemy variable bring them through different combat scenario's  and roll a D8 for player dmg and a D6 for enemy
function combat() {
    global $enemy, $playerHP, $goblinHP, $zombieHP, $mimicHP;

    while ($playerHP > 0) {
        $playerDmg = rand(1, 8);
        $enemyDmg = rand(1, 6);

        switch ($enemy) {
            case 1:
                if ($goblinHP <= 0) return;
                $goblinHP -= $playerDmg;
                $playerHP -= $enemyDmg;
                echo "You hit the goblin for $playerDmg. It hits you for $enemyDmg. Goblin HP: $goblinHP. Your HP: $playerHP\n";
                if ($goblinHP <= 0) echo "Goblin is dead!\n";
                break;

            case 2:
                if ($zombieHP <= 0) return;
                $zombieHP -= $playerDmg;
                $playerHP -= $enemyDmg;
                echo "You strike the zombie for $playerDmg. It bites for $enemyDmg. Zombie HP: $zombieHP. Your HP: $playerHP\n";
                if ($zombieHP <= 0) echo "Zombie is down!\n";
                break;

            case 3:
                if ($mimicHP <= 0) return;
                $mimicHP -= $playerDmg;
                $playerHP -= $enemyDmg;
                echo "You attack the mimic for $playerDmg. It chomps for $enemyDmg. Mimic HP: $mimicHP. Your HP: $playerHP\n";
                if ($mimicHP <= 0) echo "Mimic is slain!\n";
                break;
        }

        if ($playerHP <= 0) {
            death();
            return;
        }
    }
}

//modify the players hp
function adjustHP($amount) {
    $GLOBALS['playerHP'] += $amount;
    if ($GLOBALS['playerHP'] <= 0) {
        death();
    } else {
        echo "Your HP: {$GLOBALS['playerHP']}\n";
    }
}

//when they die ask if they way to play again if yes restart the game than start it back up, if not exit the game
function death() {
    echo "You have died. Game Over.\n";
    echo "Play again? (yes/no): ";
    $choice = strtolower(trim(readline()));
    if ($choice === "yes") {
        resetGame();
        startGame();
    } else {
        echo "Thanks for playing!\n";
        exit;
    }
}

//reset the variables if they wish to restart
function resetGame() {
    $GLOBALS['playerHP'] = 20;
    $GLOBALS['goblinHP'] = 10;
    $GLOBALS['zombieHP'] = 13;
    $GLOBALS['mimicHP'] = 11;
    $GLOBALS['room'] = 0;
    $GLOBALS['enemy'] = 0;
}
//start the game
startGame();

?>