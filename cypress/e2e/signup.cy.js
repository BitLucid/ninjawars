/* eslint-disable max-lines-per-function */
/* eslint-disable padded-blocks */
/* eslint-disable semi */
/// <reference types="cypress" />
// eslint-disable-next-line no-unused-vars
/* global cy Cypress describe beforeEach afterEach it */

// getting started guide:
// https://on.cypress.io/introduction-to-cypress

const zSubmitButtonSelector = '#become-a-ninja' // change in sister file too

describe('signup page', () => {
  beforeEach(() => {
    // cy.standardLogin()
  })
  afterEach(() => {
  })

  // For the signup for a randomized ninja test,
  // see the sister file signup-newbie.cy.js

  it('rejects duplicate-player-email signup', () => {
    cy.visit('/signup')
    // heading should be present on page
    cy.contains('Become a Ninja')
    cy.get('[role=heading][aria-label="Signup header"').should('be.visible')
    cy.url().should('match', /signup$/u)
    cy.get('input[type=email]').type(`${Cypress.env('TEST_USERNAME')}`)
    cy.get('input[type=password]').first().type(Cypress.env('TEST_PASSWORD'), { log: false })
    cy.get('input[type=password][name=cpass]').type(Cypress.env('TEST_PASSWORD'), { log: false })
    const random = (Math.random() + 1).toString(36).substring(7);
    cy.get('input[name=send_name]').type(`cypress-test-user${random}`)
    cy.get(zSubmitButtonSelector).should('be.visible')
    cy.get(zSubmitButtonSelector).click()
    cy.get('[role=alert]').should('be.visible')
  })

  it('allows login of the user', () => {
    cy.visit('/login')
    cy.customLogin(Cypress.env('TEST_USERNAME'), Cypress.env('TEST_PASSWORD'))
    cy.get('.login-form').should('not.exist')
    cy.log('Checking for the incorrect username/password alert...')
    cy.get('[role=alert]').should('not.exist')
  })

});
