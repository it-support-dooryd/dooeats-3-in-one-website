# Background Image Setup Instructions

## Option 1: Use a Placeholder (Quick Fix)

Create a simple SVG placeholder:

```html
<!-- Save this as: /Web Apps/public/images/auth-illustration.svg -->
<svg width="600" height="800" xmlns="http://www.w3.org/2000/svg">
  <defs>
    <linearGradient id="grad1" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" style="stop-color:#047857;stop-opacity:0.2" />
      <stop offset="100%" style="stop-color:#dc2626;stop-opacity:0.2" />
    </linearGradient>
  </defs>
  
  <!-- Background -->
  <rect width="600" height="800" fill="url(#grad1)"/>
  
  <!-- Delivery Scooter (Simple) -->
  <circle cx="300" cy="400" r="150" fill="#047857" opacity="0.3"/>
  <circle cx="300" cy="400" r="100" fill="#047857" opacity="0.5"/>
  <circle cx="300" cy="400" r="50" fill="#047857"/>
  
  <!-- Food Icons (Simple circles) -->
  <circle cx="200" cy="250" r="30" fill="#dc2626" opacity="0.7"/>
  <circle cx="400" cy="300" r="25" fill="#dc2626" opacity="0.7"/>
  <circle cx="350" cy="500" r="35" fill="#dc2626" opacity="0.7"/>
  
  <!-- Text -->
  <text x="300" y="650" font-family="Inter, sans-serif" font-size="32" font-weight="bold" fill="#047857" text-anchor="middle">
    Dooeats
  </text>
  <text x="300" y="690" font-family="Inter, sans-serif" font-size="18" fill="#6b7280" text-anchor="middle">
    Food Delivery Made Easy
  </text>
</svg>
```

Then update the image path in all auth files to use `.svg`:
```html
<img src="{{ asset('images/auth-illustration.svg') }}" alt="Food Delivery" class="auth-image">
```

## Option 2: Download from Free Resources

### Recommended Sites:
1. **unDraw** (https://undraw.co/illustrations)
   - Search for: "delivery", "food", "restaurant"
   - Download SVG
   - Customize colors to match theme

2. **Freepik** (https://www.freepik.com/)
   - Search for: "food delivery illustration"
   - Filter by: Free, Vector
   - Download and save as `auth-illustration.png`

3. **Flaticon** (https://www.flaticon.com/)
   - Search for: "food delivery"
   - Download as PNG (600x800px recommended)

### Steps:
1. Download image
2. Rename to `auth-illustration.png` or `auth-illustration.svg`
3. Save to: `/Web Apps/public/images/`
4. Optimize image (< 200KB)

## Option 3: Use Existing Logo/Branding

If you have existing Dooeats branding:

```html
<!-- Use logo with decorative elements -->
<div class="auth-image-content" style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%;">
  <img src="{{ asset('images/logo_web.png') }}" alt="Dooeats" style="width: 200px; margin-bottom: 2rem;">
  
  <!-- Add decorative circles -->
  <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-top: 2rem;">
    <div style="width: 60px; height: 60px; border-radius: 50%; background: #047857; opacity: 0.3;"></div>
    <div style="width: 60px; height: 60px; border-radius: 50%; background: #dc2626; opacity: 0.3;"></div>
    <div style="width: 60px; height: 60px; border-radius: 50%; background: #047857; opacity: 0.3;"></div>
  </div>
  
  <h2 class="auth-image-title">Welcome to Dooeats</h2>
  <p class="auth-image-description">Your favorite food, delivered fast</p>
</div>
```

## Option 4: Hide Image Panel (Temporary)

If you want to launch without an image:

```css
/* Add to auth-styles.css */
@media (min-width: 968px) {
  .auth-image-panel {
    display: none; /* Hide image panel */
  }
  
  .auth-form-panel {
    max-width: 600px; /* Center form */
    margin: 0 auto;
  }
}
```

## Quick Fix Command

Create a simple placeholder file:

```bash
# Navigate to images directory
cd "/Users/enohmichael/Dooeats version 2.0/Web Apps/public/images"

# Create a simple placeholder (if you have ImageMagick installed)
convert -size 600x800 gradient:#047857-#dc2626 -blur 0x8 auth-illustration.png

# OR create an empty file to prevent 404 errors
touch auth-illustration.png
```

## Recommended: Use CSS Background Instead

Update the auth-image-panel in `auth-styles.css`:

```css
.auth-image-panel {
    flex: 1;
    background: linear-gradient(135deg, 
        rgba(4, 120, 87, 0.1) 0%, 
        rgba(220, 38, 38, 0.1) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 3rem;
    position: relative;
    overflow: hidden;
}

.auth-image-panel::before {
    content: '';
    position: absolute;
    width: 400px;
    height: 400px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(4, 120, 87, 0.2) 0%, transparent 70%);
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}

.auth-image-content {
    position: relative;
    z-index: 1;
    text-align: center;
}

/* Remove the img tag requirement */
.auth-image {
    display: none;
}
```

This creates a nice gradient background without needing an actual image file.

---

**Choose the option that works best for your timeline and resources!**
