# Interactive Games Documentation

This document explains how to create and manage interactive games in the Sarah Loz Theme.

## Overview

The Sarah Loz Theme includes a built-in framework for creating interactive educational games for children. There are two types of games supported:

1. **Embedded Games**: External games that are embedded using iframe or embed code
2. **Interactive Games**: Built-in games created with the theme's game framework

## Creating a New Game

1. In the WordPress dashboard, go to **Games > Add New**
2. Enter a title for your game
3. Add a description in the main content area
4. Set a featured image (this will be displayed as the game thumbnail)
5. In the **Game Details** section, choose the appropriate settings:
   - **Age Range**: Target age group for the game
   - **Difficulty Level**: Easy, Medium, or Hard
   - **Game Type**: Choose between "External Embed" or "Interactive Game"

## External Embedded Games

If you selected "External Embed" as the game type:

1. In the **Game URL/Embed Code** field, paste either:
   - A URL to an external game (e.g., from a game hosting site)
   - An iframe embed code for the game
   - An HTML5 game embed code

## Interactive Games

If you selected "Interactive Game" as the game type:

1. Choose an **Interactive Game Type** from the dropdown:
   - Memory Game
   - Quiz Game
   - Sorting Game
   - (More game types will be added in future updates)

2. Configure the game-specific settings based on the selected type:

### Memory Game Configuration

- **Card Back Image**: Upload an image to be used for the back of the cards
- **Card Pairs**: Add image pairs that players will need to match
  - Click "Add Pair" to add more pairs
  - Upload an image for each pair
- **Match Sound**: (Optional) Upload a sound file to play when a match is found
- **Error Sound**: (Optional) Upload a sound file to play when a non-match is selected

### Quiz Game Configuration

- **Time Per Question**: Set the time limit per question (set to 0 for no limit)
- **Shuffle Questions**: Enable to randomize question order
- **Shuffle Answers**: Enable to randomize answer order
- **Show Explanations**: Enable to show explanations after answering
- **Questions**: Add quiz questions
  - **Question Text**: The question to display
  - **Question Image**: (Optional) Add an image related to the question
  - **Question Difficulty**: Set difficulty level for this specific question
  - **Explanation**: (Optional) Explanation to show after answering
  - **Answers**: Add possible answers
    - **Answer Text**: The text to display for this answer
    - **Is Correct**: Check this for the correct answer(s)

### Sorting Game Configuration

- **Sequence Mode**: Toggle between two game modes:
  - When enabled: Players sort items in sequential order (e.g., ordering numbers or steps in a process)
  - When disabled: Players sort items into categories (e.g., classifying animals, shapes, or colors)
- **Allow Incorrect Placements**: When enabled, players can place items in incorrect categories/positions
- **Correct Placement Sound**: (Optional) Sound to play when an item is placed correctly
- **Error Sound**: (Optional) Sound to play when an item is placed incorrectly
- **Categories**: (Only visible when Sequence Mode is off) Define categories for sorting
  - **Category ID**: Unique identifier for the category (no spaces)
  - **Category Name**: Display name for the category
- **Items**: Add items to be sorted
  - **Item ID**: Unique identifier for the item (no spaces)
  - **Item Text**: Text to display on the item (if no image is used)
  - **Item Image**: Upload an image for the item
  - **Item Difficulty**: Set the difficulty level for this item
  - **Item Category**: (Only visible when Sequence Mode is off) Enter the Category ID this item belongs to
  - **Item Position**: (Only visible when Sequence Mode is on) The correct position in the sequence (1, 2, 3, etc.)

## General Game Settings

Configure general settings that apply to all interactive games:

- **Game Width**: Width of the game container in pixels
- **Game Height**: Height of the game container in pixels
- **Background Color**: Choose a background color for the game
- **Show Score**: Enable to display the player's score
- **Show Timer**: Enable to display a timer during gameplay
- **Auto Start Game**: Enable to start the game automatically without requiring the player to click "Start"

## Game Statistics

The theme includes a comprehensive statistics system for tracking game plays and scores:

1. Go to **Games > Game Statistics** to view overall game statistics
2. Filter by specific game to see detailed stats for that game
3. View the leaderboard for each game showing top players
4. See total plays, high scores, and player performance

## Adding a Game Leaderboard to a Page

You can display a leaderboard for a specific game on any page using the shortcode:

```
[game_leaderboard game_id="123" limit="10" title="Top Players"]
```

Parameters:
- **game_id**: The ID of the game (if omitted on a game page, it will use the current game)
- **limit**: Number of players to display (default: 10)
- **title**: Custom title for the leaderboard (default: "قائمة المتصدرين")

## Extending the Framework

Developers can extend the interactive games framework by:

1. Creating new game types by extending the base `GameFramework` class
2. Adding new game files to the `assets/js/games/` directory
3. Updating the post-types.php file to add new game type options
4. Updating the single-game.php template to support the new game type

## Troubleshooting

If a game isn't working properly:

1. Check browser console for JavaScript errors
2. Verify that all required fields are completed in the game configuration
3. Make sure all image and sound files are correctly uploaded
4. Test the game in different browsers to identify compatibility issues
5. For memory games, ensure you've added enough card pairs for the selected difficulty level
6. For quiz games, verify that at least one answer is marked as correct for each question
7. For sorting games, verify that each item has either a category assigned (for category mode) or a position (for sequence mode) 