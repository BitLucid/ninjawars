module.exports = {
  ci: {
    collect: {
      staticDistDir: 'deploy/www',
      isSinglePageApplication: true,
      numberOfRuns: 1
    },
    upload: {
      // target: 'filesystem'
      target: 'temporary-public-storage'
    },
    assert: {
      "preset": "lighthouse:no-pwa", // Change this to "lighthouse:recommended" when we move to a PWA in the future
      assertions: {
        "maskable-icon": "off",
        "service-worker": "off",
        "tap-targets": "off",
        "apple-touch-icon": "off",
        "first-contentful-paint": ['warn', { minScore: 0.9 }],
        "interactive": ['warn', { minScore: 0.9 }],
        "last-contentful-paint": ['warn'],
        "largest-contentful-paint": ['warn'],
        "first-meaningful-paint": ['warn'],
        "label": ['warn'],
        "max-potential-fid": ['warn', { minScore: 0.9 }],
        //"render-blocking-resources": ['warn', { minScore: 0.4 }],
        "speed-index": ['warn', { minScore: 0.9 }],
        "mainthread-work-breakdown": ['warn', { minScore: 0.9 }],
        "legacy-javascript": ['warn', { auditRan: 1 }],
        "duplicated-javascript": ['warn'],
        "unused-javascript": ['warn', { maxLength: 4 }],
        "unminified-javascript": ['warn'],
        "uses-long-cache-ttl": ['warn', { maxLength: 13 }],
        "uses-rel-preconnect": ['warn'],
        "render-blocking-resources": ['error', { maxLength: 2 }],
        "font-size": ['warn'],
        "bootup-time": ['warn', { minScore: 0.65 }],
        "button-name": ['warn', { minScore: 0.65 }],
        "link-name": ['warn', { minScore: 0.65 }],
        "color-contrast": ['warn', { minScore: 0.65 }],
        "robots-txt": ['warn'],
        "first-cpu-idle": ['warn', { minScore: 0.85 }],
        "meta-description": ['warn'],
      }
    },
  }
}
