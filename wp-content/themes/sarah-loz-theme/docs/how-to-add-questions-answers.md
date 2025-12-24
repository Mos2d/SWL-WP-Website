# How to Add Questions and Answers to Audio Games

## Quick Start Guide

### Step 1: Create New Game
1. Go to **WordPress Admin → Games → Add New**
2. Enter game title: "My Audio Game"
3. Add description and featured image

### Step 2: Configure Game Type
1. **Game Type**: Interactive Game
2. **Interactive Game Type**: Audio Matching Game
3. **Game Template**: Choose any template OR Custom

### Step 3: Add Questions & Answers

#### The "Game Questions & Answers" Section
**Location**: Scroll down to find "Game Questions & Answers" section
**Note**: This section is ALWAYS visible - you don't need to select "Custom" template

#### Adding Your First Question

1. **Click "Add Question"** button
2. **Question Title**: Enter short title (e.g., "Red Color", "Cat Sound")
3. **Audio Content**: Choose how the question will be played:
   - **Text-to-Speech**: Enter text like "أحمر" (red)
   - **Audio File**: Upload MP3/WAV file
4. **Question Category**: Choose type (color, animal, etc.)

#### Adding Answer Options

1. **Click "Add Answer Option"** (minimum 2, maximum 6)
2. **For each option**:
   - **Answer Text**: Enter the text (e.g., "أحمر", "أزرق")
   - **Correct?**: Mark ONE option as correct ✅
   - **Color**: Optional background color
   - **Emoji**: Optional emoji (🔴, 🔵)
   - **Image**: Optional image upload

### Example: Simple Color Game

#### Question 1: Red Color
```
Question Title: "Red Color"
Audio Content: Text-to-Speech "أحمر"
Category: Color

Answer Options:
1. Text: "أحمر" | Correct: ✅ | Color: #FF0000 | Emoji: 🔴
2. Text: "أزرق" | Correct: ❌ | Color: #0000FF | Emoji: 🔵  
3. Text: "أخضر" | Correct: ❌ | Color: #00FF00 | Emoji: 🟢
4. Text: "أصفر" | Correct: ❌ | Color: #FFFF00 | Emoji: 🟡
```

#### Question 2: Cat Sound
```
Question Title: "Cat Sound"  
Audio Content: Upload "cat-meow.mp3" file
Category: Animal

Answer Options:
1. Text: "قطة" | Correct: ✅ | Image: cat.jpg
2. Text: "كلب" | Correct: ❌ | Image: dog.jpg
3. Text: "بقرة" | Correct: ❌ | Image: cow.jpg
```

## Advanced Features

### Mixed Template + Custom Questions
- **Template**: Choose "Colors" to get pre-built color questions
- **Custom Questions**: Add your own additional questions
- **Result**: Game will have both template AND your custom questions

### Audio Options for Questions

#### Text-to-Speech
- Enter Arabic text to be spoken
- Uses your speech settings (rate, pitch, language)
- Example: "صوت القطة" or "اللون الأحمر"

#### Upload Audio File
- **Formats**: MP3, WAV, OGG, M4A
- **Recommended**: MP3 format, under 5MB
- **Content**: Record your own voice or use sound effects
- **Example**: Cat meowing sound, color pronunciation

### Visual Answer Options

#### Background Colors
- Use color picker for answer buttons
- Perfect for color-learning games
- Example: Red button for "أحمر" answer

#### Emojis
- Add relevant emojis to make answers visual
- Examples: 🔴 for red, 🐱 for cat, 🍎 for apple
- Kids love visual cues!

#### Images
- Upload small images for each answer
- Perfect for animal/object recognition
- Keep images under 100KB for fast loading

## Common Examples

### 1. Colors Game
```
Template: Colors (gets basic red/blue)
Custom Questions: Add purple, orange, pink
Audio: Text-to-speech Arabic color names
Visual: Colored backgrounds + emojis
```

### 2. Animals Game  
```
Template: Custom
Questions: 5 different animal sounds
Audio: Upload actual animal sound files
Visual: Animal photos for each answer
```

### 3. Numbers Game
```
Template: Numbers (if available) 
Custom Questions: Add Arabic numbers 1-10
Audio: Text-to-speech Arabic numbers
Visual: Number emojis (1️⃣, 2️⃣, 3️⃣)
```

### 4. Mixed Learning Game
```
Template: Custom
Questions: Mix colors + animals + numbers
Audio: Mix of uploaded files + text-to-speech
Visual: Mix of colors, emojis, and images
```

## Tips for Success

### Question Creation
1. **Keep it simple**: 2-4 answer options work best for young kids
2. **Clear audio**: Speak slowly and clearly if recording
3. **Obvious differences**: Make wrong answers clearly different
4. **Visual cues**: Use colors/emojis to help non-readers

### Audio Quality
1. **Consistent volume**: Normalize all uploaded audio
2. **Clear speech**: Record in quiet environment
3. **Child-friendly voice**: Warm, encouraging tone
4. **File size**: Keep under 2MB for faster loading

### Answer Design
1. **One correct answer**: Mark exactly one option as correct
2. **Logical distractors**: Wrong answers should be plausible but clearly wrong
3. **Visual hierarchy**: Use colors/images to make correct answer findable
4. **Cultural relevance**: Use familiar objects/animals for your audience

## Troubleshooting

### "Questions section not showing"
- **Solution**: The section is always visible now. Scroll down to find "Game Questions & Answers"

### "Can't mark answer as correct"
- **Solution**: Use the toggle switch in the "Correct?" column

### "Audio not playing"
- **Solution**: Check file format (use MP3), ensure under 5MB

### "Game shows template questions only"
- **Solution**: Make sure you clicked "Add Question" and saved the post

### "No questions appear in game"
- **Solution**: Check that you published (not just saved as draft) the game

## Quick Checklist

Before publishing your game:
- [ ] Added at least 2 questions
- [ ] Each question has 2-6 answer options  
- [ ] Exactly ONE answer per question is marked correct
- [ ] Audio content is set (text or file) for each question
- [ ] Game is published (not draft)
- [ ] Tested the game on the frontend

Your questions and answers are now ready! Kids can listen to the audio and select the correct visual answer.
