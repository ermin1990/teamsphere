# TeamSphere PWA (Progressive Web App) - Beta Version

## Overview

TeamSphere has been configured as a Progressive Web App (PWA), allowing users to install it on their devices for a native app-like experience.

## Features

### ✅ Implemented Features

1. **Web App Manifest** (`/public/manifest.json`)
   - App name: "TeamSphere (Beta)"
   - Standalone display mode
   - Custom theme colors (dark mode by default)
   - Icon configuration for multiple sizes
   - Shortcuts for quick access to key features

2. **Service Worker** (`/public/sw.js`)
   - **Network-First Strategy**: Always fetches fresh content from the network
   - **Minimal Caching**: Only caches essential assets (favicon, manifest)
   - **5-Minute Cache Duration**: Short cache lifetime for beta testing
   - **Never-Cache URLs**: API endpoints and Livewire requests always fresh
   - **Auto-Update Check**: Checks for updates every 60 seconds
   - **Smart Cache Management**: Automatically removes old caches

3. **Beta Version Labeling**
   - Title tags show "(Beta)" suffix
   - Navigation header displays "Team Sphere (Beta)"
   - Clear indication this is a beta version

4. **PWA Meta Tags**
   - Apple mobile web app capable
   - Theme color for mobile browsers
   - Proper viewport configuration
   - Apple touch icons

5. **Auto-Update Mechanism**
   - Detects new service worker versions
   - Prompts user to update when available
   - Automatic page reload after update

## Installation

### Mobile Devices (Android/iOS)

#### Android (Chrome/Edge)
1. Open TeamSphere in Chrome or Edge browser
2. Tap the three-dot menu (⋮)
3. Select "Install app" or "Add to Home screen"
4. Follow the prompts to complete installation

#### iOS (Safari)
1. Open TeamSphere in Safari browser
2. Tap the Share button (□↑)
3. Scroll down and tap "Add to Home Screen"
4. Name the app and tap "Add"

### Desktop (Chrome/Edge)
1. Open TeamSphere in Chrome or Edge
2. Look for the install icon (➕) in the address bar
3. Click the icon and confirm installation
4. The app will open in a standalone window

## Cache Strategy (Beta Version)

### Network-First Approach
To ensure developers see changes immediately during beta testing:

- **Primary Strategy**: Always fetch from network first
- **Fallback**: Use cache only when network is unavailable
- **Cache Duration**: 5 minutes maximum
- **Never Cached**:
  - `/livewire/*` - Livewire components
  - `/api/*` - API endpoints
  - `/sanctum/*` - Authentication
  - `/broadcasting/*` - Real-time features

### Development Mode
When `APP_ENV=local`, service worker is automatically unregistered to prevent caching issues.

### Production Mode
When `APP_ENV=production`, service worker is registered with:
- Auto-update checks every 60 seconds
- User prompts for new versions
- Automatic cache cleanup

## Manual Cache Control

### Clear All Caches
Open browser console and run:
```javascript
navigator.serviceWorker.controller.postMessage({ type: 'CLEAR_CACHE' });
```

### Force Update Service Worker
```javascript
navigator.serviceWorker.getRegistrations().then(registrations => {
  registrations.forEach(registration => registration.update());
});
```

### Unregister Service Worker (Development)
```javascript
navigator.serviceWorker.getRegistrations().then(registrations => {
  registrations.forEach(registration => registration.unregister());
});
```

## Icons

### Required Icons
The following icon sizes should be generated for optimal PWA support:
- 72x72, 96x96, 128x128, 144x144, 152x152, 192x192, 384x384, 512x512

### Current Status
Currently using favicon.ico as fallback. See `/public/icons/README.md` for instructions on generating proper PWA icons.

## Testing

### PWA Audit
Use Lighthouse in Chrome DevTools:
1. Open DevTools (F12)
2. Go to "Lighthouse" tab
3. Select "Progressive Web App"
4. Click "Analyze page load"

### Service Worker Status
Check service worker status:
1. Open DevTools (F12)
2. Go to "Application" tab
3. Click "Service Workers" in the left sidebar
4. View registration status and cache storage

### Manifest Validation
Check manifest:
1. Open DevTools (F12)
2. Go to "Application" tab
3. Click "Manifest" in the left sidebar
4. Review manifest properties

## Deployment Checklist

Before deploying to production:

- [ ] Generate and add proper PWA icons to `/public/icons/`
- [ ] Update `manifest.json` with actual icon paths
- [ ] Test installation on Android device
- [ ] Test installation on iOS device
- [ ] Test installation on Desktop Chrome/Edge
- [ ] Verify cache strategy is working correctly
- [ ] Test offline functionality
- [ ] Verify auto-update prompts work
- [ ] Run Lighthouse PWA audit (should score 90+)
- [ ] Test on slow network connections

## Known Limitations (Beta)

1. **Minimal Offline Support**: App requires network for most features
2. **Short Cache Duration**: 5-minute cache may cause more network requests
3. **Icon Placeholders**: Using favicon until proper icons are generated
4. **Frequent Update Checks**: 60-second intervals may impact performance

## Future Enhancements

### Phase 1 (Current - Beta)
- ✅ Basic PWA setup
- ✅ Manifest configuration
- ✅ Network-first caching
- ✅ Auto-update mechanism
- ✅ Beta version labeling

### Phase 2 (Production Ready)
- [ ] Generate proper PWA icons
- [ ] Implement smart caching for static assets
- [ ] Add offline fallback pages
- [ ] Implement background sync
- [ ] Add push notifications
- [ ] Optimize cache duration based on asset type

### Phase 3 (Advanced Features)
- [ ] Share target API for sharing content
- [ ] Web Share API integration
- [ ] Badge API for notifications
- [ ] Periodic background sync
- [ ] Advanced offline capabilities

## Troubleshooting

### App Not Installing
1. Ensure you're using HTTPS (required for PWA)
2. Check that manifest.json is accessible
3. Verify service worker is registered
4. Check browser console for errors

### Updates Not Showing
1. Clear browser cache
2. Unregister service worker
3. Hard reload (Ctrl+Shift+R or Cmd+Shift+R)
4. Re-register service worker

### Cache Issues During Development
1. Set `APP_ENV=local` in `.env`
2. Service worker will be automatically unregistered
3. Alternatively, manually unregister via DevTools

## Resources

- [MDN PWA Guide](https://developer.mozilla.org/en-US/docs/Web/Progressive_web_apps)
- [Google PWA Checklist](https://web.dev/pwa-checklist/)
- [PWA Builder](https://www.pwabuilder.com/)
- [Service Worker Cookbook](https://serviceworke.rs/)

## Support

For issues or questions about the PWA implementation, please:
1. Check this documentation
2. Review browser console for errors
3. Use the "Prijavi grešku | Sugestija" feature in the app
4. Contact the development team

---

**Version**: Beta v1.0.0  
**Last Updated**: November 2025  
**Status**: Active Development
