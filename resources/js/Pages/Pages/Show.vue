<template>
  <div>
    <h1>{{ page.title }}</h1>
    <img :src="page.image" alt="Page Image">
    <p>{{ page.description }}</p>
  </div>
</template>

<script>
export default {
  props: {
    page: Object,
  },
};
</script>
