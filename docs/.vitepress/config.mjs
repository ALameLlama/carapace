import { defineConfig } from 'vitepress'

// https://vitepress.dev/reference/site-config
export default defineConfig({
  title: "Carapace",
  description: "Framework-agnostic DTOs for PHP",
  themeConfig: {
    search: {
      provider: 'local'
    },
    // https://vitepress.dev/reference/default-theme-config
    nav: [
      { text: 'Home', link: '/' },
      { text: 'Guide', link: '/guide/' },
      { text: 'Attributes', link: '/attributes/' }
    ],

    sidebar: [
      {
        text: 'Guide',
        items: [
          { text: 'Getting Started', link: '/guide/' },
          { text: 'Creating DTOs', link: '/guide/from' },
          { text: 'Updating DTOs', link: '/guide/with' },
        ]
      },
      {
        text: 'Attributes',
        items: [
          { text: 'Overview', link: '/attributes/' },
          { text: 'CastWith', link: '/attributes/cast-with' },
          { text: 'MapFrom', link: '/attributes/map-from' },
          { text: 'MapTo', link: '/attributes/map-to' },
          { text: 'Hidden', link: '/attributes/hidden' }
        ]
      },
      {
        text: 'Advanced',
        items: [
          { text: 'Custom Casters', link: '/advanced/custom-casters' },
          { text: 'Combining Attributes', link: '/advanced/combining-attributes' }
        ]
      },
      {
        text: 'Contributing',
        items: [
          { text: 'Local Setup', link: '/contributing/local-setup' },
          { text: 'Testing', link: '/contributing/testing' }
        ]
      }
    ],

    socialLinks: [
      { icon: 'github', link: 'https://github.com/alamellama/carapace' }
    ],
  },
  base: '/carapace/',
  cleanUrls: true,
  lastUpdated: true,
})
