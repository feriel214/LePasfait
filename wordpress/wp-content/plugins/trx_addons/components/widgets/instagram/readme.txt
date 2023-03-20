This app used in our plugin "ThemeREX Socials" to show recent user's posts from Instagram, filtered by hashtag.
The app send queries only to follow endpoints:
1. If hashtag specified - GET /tags/tag-name/media/recent to show 
2. If no hashtag specified - GET /users/self/media/recent