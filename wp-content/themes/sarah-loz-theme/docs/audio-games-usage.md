# Audio Matching Games - Usage Guide

## Overview
The audio matching games have been fully integrated into the existing WordPress game system. You can now create audio games directly through the WordPress admin interface alongside other interactive games.

## Creating Audio Games

### Step 1: Create New Game
1. Go to **WordPress Admin → Games → Add New**
2. Enter your game title and description
3. Add a featured image (optional but recommended)

### Step 2: Configure Game Settings
1. In the **Game Details** section:
   - **Age Range**: Select appropriate age group (3-5, 6-7, 8-9)
   - **Difficulty Level**: Choose Easy, Medium, or Hard
   - **Game Type**: Select "Interactive Game"
   - **Interactive Game Type**: Choose "Audio Matching Game"

### Step 3: Audio Game Configuration
Once you select "Audio Matching Game", you'll see the audio game settings:

#### Game Template
Choose from pre-built templates or create custom content:
- **Colors (الألوان)**: Learn color names
- **Animals (الحيوانات)**: Animal sounds
- **Numbers (الأرقام)**: Number recognition  
- **Shapes (الأشكال)**: Geometric shapes
- **Fruits (الفواكه)**: Fruit names
- **Custom (مخصص)**: Create your own questions

#### Speech Settings
- **Speech Language**: Arabic variants (Saudi, Egypt, UAE, Generic)
- **Speech Rate**: Speed of speech (0.1 = very slow, 2.0 = very fast)
- **Speech Pitch**: Voice pitch (0.0 = low, 2.0 = high)

#### Game Options
- **Shuffle Options**: Randomize answer choices
- **Max Attempts per Question**: Number of tries allowed (1-10)
- **Auto Play Intro**: Automatically play introduction
- **Show Feedback**: Display success/error messages

### Step 4: Custom Questions (if using Custom template)
When you select "Custom" template, you can create your own questions:

#### For Each Question:
1. **Question Text**: The visible identifier
2. **Audio Content**: Choose between:
   - **Text-to-Speech**: Enter text to be spoken
   - **Audio File**: Upload MP3, WAV, OGG, or M4A files
3. **Question Category**: Classify the question type
4. **Answer Options** (2-6 options):
   - Option text (required)
   - Correct answer (select one)
   - Background color (optional)
   - Emoji/Icon (optional)
   - Image (optional)

### Step 5: General Game Settings
Configure the visual appearance:
- **Game Width/Height**: Dimensions in pixels
- **Background Color**: Game background
- **Show Score**: Display scoring
- **Show Timer**: Display elapsed time
- **Auto Start Game**: Start automatically

## Example Configurations

### Simple Color Game
```
Game Type: Interactive Game
Interactive Game Type: Audio Matching Game
Template: Colors
Speech Language: Arabic (Saudi)
Speech Rate: 0.8
Max Attempts: 3
```

### Custom Animal Sounds Game
```
Game Type: Interactive Game
Interactive Game Type: Audio Matching Game
Template: Custom

Question 1:
- Text: "قطة"
- Audio: Upload "cat-meow.mp3" file
- Options: قطة (correct), كلب, بقرة, خروف

Question 2: 
- Text: "كلب"
- Audio: Text-to-Speech "هاو هاو - هذا صوت الكلب"
- Options: قطة, كلب (correct), بقرة, خروف
```

## File Upload Requirements

### Audio Files
- **Supported formats**: MP3, WAV, OGG, M4A
- **Recommended**: MP3 format for best compatibility
- **File size**: Keep under 5MB for better performance
- **Quality**: 128-320 kbps for good audio quality

### Images (for options)
- **Supported formats**: JPG, PNG, GIF
- **Recommended size**: 150x150 pixels
- **Use**: Icons, illustrations, or photos

## Shortcode Usage

You can also embed games in posts/pages using shortcodes:

```php
// Embed specific game by ID
[audio_game id="123" width="100%" height="500px"]

// Use template with custom settings
[audio_game template="colors" theme="ocean" auto_start="true"]
```

## Best Practices

### Audio Content
1. **Keep it short**: 2-3 seconds per audio clip
2. **Clear pronunciation**: Especially for young children
3. **Consistent volume**: Normalize audio levels
4. **Test playback**: Verify on different devices

### Question Design
1. **Age-appropriate**: Match content to target age group
2. **Clear options**: Distinct and unambiguous choices
3. **Visual cues**: Use colors, emojis, or images
4. **Progressive difficulty**: Start easy, increase complexity

### Technical Considerations
1. **Mobile-first**: Test on phones and tablets
2. **Loading time**: Optimize file sizes
3. **Browser support**: Test text-to-speech functionality
4. **Accessibility**: Provide visual feedback for audio

## Troubleshooting

### Audio Not Playing
- Check browser permissions for audio
- Verify file format compatibility
- Test text-to-speech in browser developer tools
- Ensure proper file upload to WordPress media library

### Game Not Loading
- Check JavaScript console for errors
- Verify all required scripts are loaded
- Confirm ACF fields are properly configured
- Test with default templates first

### Performance Issues
- Reduce audio file sizes
- Limit number of questions per game
- Optimize images used in options
- Test on target devices

## Support

For additional help:
1. Check the WordPress admin Games statistics page
2. Test games with the preview functionality
3. Review browser console for technical errors
4. Verify ACF field configurations match this guide
