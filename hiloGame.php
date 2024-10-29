<?php

/* File: hiloGame.php
Project: PROG2001-24F-Sec1-Web Design 
Programmer: Lukas Fukuoka Vieira
First version: 21 of Oct, 2024
Description: This file contains the PHP code for the Hi-Lo Guessing Game. 
It handles the game logic, user input, and session management. 
The game allows the user to set a maximum range for guessing a random number. 
The user can then submit guesses within the range to find the random number. 
The game provides feedback on whether the guess is too high or too low and congratulates the user when the correct number is guessed. 
The user can play again to start a new game. 
*/

session_start(); // Start a new session or resume the existing session

// Function to reset the game session (except for the username)
function reset_game() {
    // Unset all session variables related to the game to reset the state
    unset($_SESSION['minRange']);
    unset($_SESSION['maxRange']);
    unset($_SESSION['randomNumber']);
    unset($_SESSION['rangeSet']);
    unset($_SESSION['gameOver']);
}

// If the user's name is not set, redirect them back to the start page
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['username'])) {
    // Sanitize and store the user's name
    $username = htmlspecialchars(trim($_POST['username']));
    $_SESSION['username'] = $username;
    reset_game(); // Reset the game whenever the user restarts
} elseif (!isset($_SESSION['username'])) {
    // Redirect back to start page if no username is set in session
    header("Location: hiloStart.html");
    exit();
}

// If the range is set, process the guess
$message = ""; // Variable to store messages for the user
$win = false; // Flag to indicate if the user has won
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['range'])) {
    // User sets a range for guessing
    $range = intval($_POST['range']); // Convert the range input to an integer
    if ($range > 1 && $range <= 999) {
        // Set the minimum and maximum range based on the user's input
        $_SESSION['minRange'] = 1;
        $_SESSION['maxRange'] = $range;
        // Generate a random number within the set range
        $_SESSION['randomNumber'] = rand($_SESSION['minRange'], $_SESSION['maxRange']);
        $_SESSION['rangeSet'] = true; // Indicate that the range is set
    } else {
        // If the input range is invalid, set an error message
        $message = "Please enter a valid range greater than 1.";
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['guess'])) {
    // User submits a guess
    $guess = intval($_POST['guess']); // Convert the guess input to an integer
    if ($guess < $_SESSION['minRange'] || $guess > $_SESSION['maxRange']) {
        // If the guess is out of the current range, set an error message
        $message = "Your guess is out of range!";
    } elseif ($guess > $_SESSION['randomNumber']) {
        // If the guess is too high, adjust the maximum range and set a message
        $message = "Too high! Try guessing between {$_SESSION['minRange']} and " . ($guess - 1) . ".";
        $_SESSION['maxRange'] = $guess - 1;
    } elseif ($guess < $_SESSION['randomNumber']) {
        // If the guess is too low, adjust the minimum range and set a message
        $message = "Too low! Try guessing between " . ($guess + 1) . " and {$_SESSION['maxRange']}.";
        $_SESSION['minRange'] = $guess + 1;
    } else {
        // If the guess is correct, congratulate the user and mark the game as won
        $message = "Congratulations! You guessed the number!";
        $win = true;
        $_SESSION['gameOver'] = true;
    }
}

// If the game is over and the user clicks "Play Again"
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['playAgain'])) {
    // Reset the game session and reload the page to start a new game
    reset_game();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Now, it's safe to output the HTML and CSS
echo '<link rel="stylesheet" type="text/css" href="hiloStyles.css">'; // Include CSS after all header() calls
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hi-Lo Guessing Game</title>
</head>
<body style="background-color: <?php echo $win ? 'green' : ''; ?>;">
    <h1>Hi-Lo Guessing Game</h1>
    
    <?php if ($win): ?>
        <!-- Show only the winning message and the waterfall effect when the user wins -->
        <h2>🎉 Congratulations, <?php echo $_SESSION['username']; ?>! 🎉</h2>
        <p><?php echo $message; ?></p>
        <div class="waterfall"></div>
        <div class="waterfall"></div>
        <div class="waterfall"></div>
        <form method="POST">
            <button type="submit" name="playAgain">Play Again</button>
        </form>
    <?php else: ?>
        <!-- Game interface when the user has not yet won -->
        <h2>Hello, <?php echo $_SESSION['username']; ?>!</h2>
        <?php if (!isset($_SESSION['rangeSet']) || !$_SESSION['rangeSet']): ?>
            <!-- Form to set the maximum guessing range -->
            <form method="POST">
                <label for="range">Set a maximum guessing range (between 1 and 999):</label>
                <input type="number" name="range" min="2" max="999" required>
                <button type="submit">Start Game</button>
            </form>
            <p><?php echo $message; ?></p>
        <?php else: ?>
            <!-- Form to submit a guess within the current range -->
            <form method="POST">
                <label for="guess">Guess a number between <?php echo $_SESSION['minRange']; ?> and <?php echo $_SESSION['maxRange']; ?>:</label>
                <input type="number" name="guess" min="<?php echo $_SESSION['minRange']; ?>" max="<?php echo $_SESSION['maxRange']; ?>" required>
                <button type="submit">Guess</button>
            </form>
            <p><?php echo $message; ?></p>
        <?php endif; ?>
    <?php endif; ?>

    <!-- Waterfall Animation -->
    <script>
        // Function to create and display waterfall effects
        function createWaterfallEffects() {
            const numberOfWaterfalls = 10; // Adjust the number of waterfall elements
            // Create Waterfalls
            for (let i = 0; i < numberOfWaterfalls; i++) {
                const waterfall = document.createElement('div');
                waterfall.classList.add('waterfall');
                waterfall.style.left = Math.random() * 80 + 10 + '%'; 
                waterfall.style.animationDuration = Math.random() * 3 + 3 + 's'; 
                document.body.appendChild(waterfall);
            }
        }

        createWaterfallEffects(); // Call the function to create the waterfall elements
    </script>
</body>
</html>
