import DefaultTheme from "vitepress/theme";
import AttributeBadges from "./components/AttributeBadges.vue";
import "./custom.css";

export default {
  extends: DefaultTheme,
  enhanceApp({ app }) {
    app.component("AttributeBadges", AttributeBadges);
  },
};
