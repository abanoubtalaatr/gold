export default {
    supportedLanguages: ["en", "ar"], // اللغات المدعومة

};
export const getImageUrl = (path) => {
    if (!path) {
        return "/dashboard-assets/img/default-avatar.png"; // الصورة الافتراضية
    }
    return `${import.meta.env.VITE_BASE_URL}/storage/${path}`;
};