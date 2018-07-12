const defaultConfig = {
    customPurposeListLocation: null,
    globalVendorListLocation: metadata.globalVendorListLocation,
    globalConsentLocation: metadata.globalConsentLocation,
    storeConsentGlobally: true,
    storePublisherData: true,
    logging: false,
    localization: {},
    forceLocale: null,
    gdprAppliesGlobally: false,
    repromptOptions: {
        fullConsentGiven: 360,
        someConsentGiven: 30,
        noConsentGiven: 30,
    },
    geoIPVendor: 'https://cmp.digitru.st/1/geoip.json',
    digitrustRedirectUrl: metadata.digitrustRedirectLocation,
    testingMode: 'normal',
    blockBrowsing: true,
    layout: null,
    showFooterAfterSubmit: true,
    logoUrl: null,
    css: {
        "color-primary": "#0a82be",
        "color-secondary": "#eaeaea",
        "color-border": "#eaeaea",
        "color-background": "#ffffff",
        "color-text-primary": "#333333",
        "color-text-secondary": "#0a82be",
        "color-linkColor": "#0a82be",
        "color-table-background": "#f7f7f7",
        "font-family": "'Helvetica Neue', Helvetica, Arial, sans-serif",
        "custom-font-url": null,
    },
    digitrust: {
        redirects: false
    }
};