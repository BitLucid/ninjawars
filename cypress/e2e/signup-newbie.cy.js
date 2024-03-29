/* eslint-disable max-lines-per-function */
/* eslint-disable padded-blocks */
/* eslint-disable semi */
/// <reference types="cypress" />
// eslint-disable-next-line no-unused-vars
/* global cy Cypress describe beforeEach afterEach it */

// getting started guide:
// https://on.cypress.io/introduction-to-cypress

const zSubmitButtonSelector = '#become-a-ninja' // change in sister file too

describe('signup a newbie ninja', () => {
  beforeEach(() => {
    // cy.standardLogin()
  })
  afterEach(() => {
    // Here for any future cleanup needs
  })

  it('allows signup (with random test-strings)', () => {
    cy.visit('/signup')
    // heading should be present on page
    cy.contains('Become a Ninja')
    cy.get('[role=heading][aria-label="Signup header"').should('be.visible')
    cy.url().should('match', /signup$/u)
    const random = (Math.random() + 1).toString(36).substring(7);
    const randomEmailLabel = `ninjawarstchalvak+cypress-testing${random}@gmail.com`
    const randomSendName = `Viper-${random}`
    cy.get('input[type=email]').type(randomEmailLabel)
    cy.get('input[type=password]').first().type(Cypress.env('TEST_PASSWORD'), { log: false })
    cy.get('input[type=password][name=cpass]').type(Cypress.env('TEST_PASSWORD'), { log: false })
    cy.get('input[name=send_name]').type(randomSendName)
    // Also pick class
    cy.get('.ninja-picker-container label').first().click()
    cy.get('.ninja-picker-container label').last().click()
    // Then submit
    cy.get(zSubmitButtonSelector).should('be.visible')
    cy.get(zSubmitButtonSelector).click()
    cy.get('[role=alert]').should('not.exist')
    cy.contains('You are almost ready to be a ninja!').should('be.visible')
    cy.contains(randomEmailLabel).should('be.visible')
    cy.visit('/login')
    cy.customLogin(randomEmailLabel, Cypress.env('TEST_PASSWORD'))
    cy.get('.avatar').should('be.visible')
    cy.get('.dropdown[role=menu]').first().click()
    cy.contains(randomSendName).should('be.visible')
    // cy.contains('Live by the Shuriken').should('be.visible')

  })

});
