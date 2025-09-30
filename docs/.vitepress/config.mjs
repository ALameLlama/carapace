import defineVersionedConfig from "vitepress-versioning-plugin";
import sidebars from "./sidebars";
import taskLists from "markdown-it-task-lists";

// https://vitepress.dev/reference/site-config
export default defineVersionedConfig(
  {
    title: "Carapace",
    description: "Framework-agnostic DTOs for PHP",
    head: [
      [
        "link",
        {
          rel: "preload",
          as: "font",
          type: "font/woff2",
          crossorigin: "",
          href: "https://cdn.jsdelivr.net/fontsource/fonts/maple-mono@latest/latin-400-normal.woff2",
        },
      ],
      [
        "link",
        {
          rel: "icon",
          href: "/art/favicon.ico",
        },
      ],
    ],
    themeConfig: {
      siteTitle: false,
      logo: { src: "/art/logo.webp", alt: "Carapace Logo" },
      search: {
        provider: "local",
        options: {
          miniSearch: {
            searchOptions: {
              // Filter results for the current version
              filter: (result) => {
                const id = result.id;
                const currentPath = window.location.pathname;

                // Match the first segment in the path, e.g. "1.x", "2.x", etc.
                const versionMatch = currentPath.split("/")[1]; // "" or "1.x"
                const entryPrefix = id.split("/")[1]; // "" or "1.x"

                if (versionMatch && versionMatch.match(/^\d+(\.x|\.\d+)?$/)) {
                  // User is inside a versioned docs section (e.g. /1.x/)
                  return entryPrefix === versionMatch;
                } else {
                  // User is in root (non-versioned docs)
                  return !entryPrefix.match(/^\d+(\.x|\.\d+)?$/);
                }
              },
            },
          },
        },
      },
      // https://vitepress.dev/reference/default-theme-config
      nav: [{ text: "Getting Started", link: "/guide/" }],

      sidebar: sidebars,

      socialLinks: [
        { icon: "github", link: "https://github.com/alamellama/carapace" },
      ],
    },
    markdown: {
      config: (md) => {
        md.use(taskLists, { enabled: true });
      },
    },
    versioning: {
      latestVersion: "2.x",
    },
    // base: '/carapace/',
    cleanUrls: true,
    lastUpdated: true,
  },
  __dirname,
);
