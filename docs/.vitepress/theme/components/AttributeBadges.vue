<script setup>
const props = defineProps({
  scope: { type: String, default: "" }, // 'class' | 'property' | 'both'
  stage: { type: String, default: "" }, // 'pre-hydration' | 'hydration'  | 'serialization' | 'deserialization'
});

const scopeMap = {
  class: { text: "Class Attribute", type: "warning" },
  property: { text: "Property Attribute", type: "warning" },
  both: { text: "Class & Property Attribute", type: "danger" },
};

const stageMap = {
  "pre-hydration": { text: "Pre-hydration Stage", type: "tip" },
  hydration: { text: "Hydration Stage", type: "tip" },
  serialization: { text: "Serialization Stage", type: "tip" },
};
</script>

<template>
  <div class="attr-badges">
    <Badge
      v-if="scope && scopeMap[scope]"
      :type="scopeMap[scope].type"
      :text="scopeMap[scope].text"
    />
    <Badge
      v-if="stage && stageMap[stage]"
      :type="stageMap[stage].type"
      :text="stageMap[stage].text"
    />
    <slot />
  </div>
</template>

<style scoped>
.attr-badges {
  display: flex;
  gap: 8px;
  align-items: center;
  margin: 8px 0 16px;
}
</style>
