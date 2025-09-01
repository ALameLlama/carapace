const sidebars = {
  "1.x/": [
    {
      text: "Guide v1.x",
      link: "/1.x/guide",
      items: [
        { text: "Creating DTOs", link: "/1.x/guide/from" },
        { text: "Collecting DTOs", link: "/1.x/guide/collect" },
        { text: "Updating DTOs", link: "/1.x/guide/with" },
      ],
    },
    {
      text: "Attributes",
      link: "/1.x/attributes",
      items: [
        { text: "CastWith", link: "/1.x/attributes/cast-with" },
        { text: "MapFrom", link: "/1.x/attributes/map-from" },
        { text: "MapTo", link: "/1.x/attributes/map-to" },
        { text: "Hidden", link: "/1.x/attributes/hidden" },
      ],
    },
    {
      text: "Advanced",
      link: "/1.x/advanced",
      items: [
        { text: "Custom Casters", link: "/1.x/advanced/custom-casters" },
        {
          text: "Combining Attributes",
          link: "/1.x/advanced/combining-attributes",
        },
        {
          text: "With() Auto Completion",
          link: "/1.x/advanced/with-autocomplete",
        },
      ],
    },
    {
      text: "Contributing",
      items: [
        { text: "Local Setup", link: "/contributing/local-setup" },
        { text: "Testing", link: "/contributing/testing" },
      ],
    },
    {
      text: "Change log",
      link: "https://github.com/ALameLlama/carapace/blob/1.x/CHANGELOG.md",
    },
  ],
  "/": [
    {
      text: "Guide",
      link: "/guide",
      collapsed: false,
      items: [
        { text: "Creating DTOs", link: "/guide/from" },
        { text: "Collecting DTOs", link: "/guide/collect" },
        { text: "Updating DTOs", link: "/guide/with" },
      ],
    },
    {
      text: "Attributes",
      link: "/attributes",
      collapsed: false,
      items: [
        {
          text: "CastWith",
          link: "/attributes/cast-with",
          collapsed: true,
          items: [
            { text: "Primitive", link: "/attributes/cast-with/primitive" },
            { text: "Enum", link: "/attributes/cast-with/enum" },
            { text: "Date & Time", link: "/attributes/cast-with/datetime" },
          ],
        },
        {
          text: "ConvertEmptyToNull",
          link: "/attributes/convert-empty-to-null",
        },
        { text: "EnumSerialize", link: "/attributes/enum-serialize" },
        { text: "GroupFrom", link: "/attributes/group-from" },
        { text: "Hidden", link: "/attributes/hidden" },
        { text: "MapFrom", link: "/attributes/map-from" },
        { text: "MapTo", link: "/attributes/map-to" },
        { text: "SnakeCase", link: "/attributes/snake-case" },
      ],
    },
    {
      text: "Advanced",
      link: "/advanced",
      collapsed: true,
      items: [
        { text: "Custom Attributes", link: "/advanced/custom-attributes" },
        { text: "Custom Casters", link: "/advanced/custom-casters" },
        {
          text: "Combining Attributes",
          link: "/advanced/combining-attributes",
        },
        {
          text: "With() Auto Completion",
          link: "/advanced/with-autocomplete",
        },
      ],
    },
    {
      text: "Contributing",
      collapsed: true,
      items: [
        { text: "Local Setup", link: "/contributing/local-setup" },
        { text: "Testing", link: "/contributing/testing" },
      ],
    },
    {
      text: "Migrate 1.x to 2.x",
      link: "/migrate",
    },
    {
      text: "Change log",
      link: "https://github.com/ALameLlama/carapace/blob/master/CHANGELOG.md",
    },
  ],
};

export default sidebars;
