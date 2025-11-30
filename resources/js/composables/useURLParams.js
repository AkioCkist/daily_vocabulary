/**
 * URL Params Composable
 * Handles parsing and managing URL query parameters
 */

import { onMounted } from 'vue';

export const useURLParams = (onSaveSessionData) => {
  const parseSessionDataFromURL = () => {
    const urlParams = new URLSearchParams(window.location.search);
    const showSavePopup = urlParams.get('show_save_popup') === 'true';

    if (showSavePopup) {
      try {
        const sessionDataParam = urlParams.get('save_session_data');
        if (sessionDataParam) {
          const parsedData = JSON.parse(decodeURIComponent(sessionDataParam));
          onSaveSessionData(parsedData);
          // Clean up URL
          window.history.replaceState({}, document.title, window.location.pathname);
          return true;
        }
      } catch (error) {
        console.error('useURLParams - Error parsing session data:', error);
      }
    }
    return false;
  };

  const initializeFromURL = () => {
    onMounted(() => {
      parseSessionDataFromURL();
    });
  };

  return {
    parseSessionDataFromURL,
    initializeFromURL
  };
};
