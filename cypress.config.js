const { defineConfig } = require('cypress');

/*
Set your username password, and webdomain as env vars in your bashrc
export CYPRESS_TEST_USERNAME="yourusername@gosweft.com"
export CYPRESS_TEST_PASSWORD="yourpasswordhere"
export CYPRESS_BASE_URL="http://localhost:8765"
export CYPRESS_TEST_FIRST_NAME="YourFirstName"
*/

// Everything is overridable via env vars CYPRESS_WHATEVER

module.exports = defineConfig({
  projectId: '749eyt',
  e2e: {
    setupNodeEvents(on, config) {
      // implement node event listeners here
    },
  },
});
