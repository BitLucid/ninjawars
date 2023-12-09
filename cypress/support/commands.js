/* eslint-disable semi */
/// <reference types="cypress" />
/* global cy Cypress */
// ***********************************************
// This example commands.js shows you how to
// create various custom commands and overwrite
// existing commands.
//
// For more comprehensive examples of custom
// commands please read more here:
// https://on.cypress.io/custom-commands
// ***********************************************
//
//
Cypress.Commands.add('customLogin', (username, password) => { // For login page testing only
  cy.contains(/login|sign in/iu, { timeout: 7000 })
  cy.get('input[type=text]').type(username)
  cy.get('input[type=password]').type(password, { log: false })
  cy.get('form input[type=submit]').click()
})

Cypress.Commands.add('standardLogin', () => {
  if (Cypress.env('TEST_USERNAME') === undefined) {
    throw new Error('CYPRESS_TEST_USERNAME is not set')
  }
  cy.session(
    Cypress.env('TEST_USERNAME'),
    () => {
      cy.visit('/login')
      cy.url().should('contain', 'login')
      cy.contains('NinjaWars Login', { timeout: 7000 })
      cy.get('input[type=text]').type(Cypress.env('TEST_USERNAME'))
      cy.get('input[type=password]').type(Cypress.env('TEST_PASSWORD'), { log: false })
      cy.get('form input[type=submit]').click()
      cy.log('Now checking if cypress login got past the login page...')
      cy.get('.login-form').should('not.exist')
      cy.log('Should be logged in now, moving on...')
    },
    {
      validate: () => {
        // Validate that any nw cookie is present
        cy.getCookie('PHPSESSID').should('exist')
        // Currently the php session id cookie is always set even when logged out, @todo
      },
    },
  )
})

Cypress.Commands.add('logout', () => {
  cy.visit('/logout')
  // wait for the url to not be logout
  cy.log('After logout it should redirect');
  cy.url().should('include', 'loggedout', { timeout: 10000 })
  cy.log('Cypress logout() command ran')
  cy.log('========= Logout should now be COMPLETE --RR =======')
})

Cypress.Commands.add('attemptResurrect', () => {
  cy.visit('/shrine')
  // wait for the url to not be logout
  cy.log('Attempting a resurrect if necessary')

  cy.url().should('include', 'alive', { timeout: 10000 })
  cy.log('Cypress attemptResurrect() command ran')
})

// -- This is a parent command --
// Cypress.Commands.add('login', (email, password) => { ... })
//
//
// -- This is a child command --
// Cypress.Commands.add('drag', { prevSubject: 'element'}, (subject, options) => { ... })
//
//
// -- This is a dual command --
// Cypress.Commands.add('dismiss', { prevSubject: 'optional'}, (subject, options) => { ... })
//
//
// -- This will overwrite an existing command --
// Cypress.Commands.overwrite('visit', (originalFn, url, options) => { ... })
