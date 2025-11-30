/**
 * Composable for animation utilities and GSAP integration
 * Encapsulates all animation-related logic for reusability
 */
export function useAnimation() {
  /**
   * Returns CSS animation classes for common transitions
   */
  const getAnimationClasses = (type = 'scale-in') => {
    const animations = {
      'scale-in': 'animate-scale-in',
      'fade-in': 'animate-fade-in',
      'slide-in': 'animate-slide-in',
    };
    return animations[type] || animations['scale-in'];
  };

  return {
    getAnimationClasses,
  };
}
