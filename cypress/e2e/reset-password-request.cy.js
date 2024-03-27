/* eslint-disable max-lines-per-function */
/* eslint-disable padded-blocks */
/* eslint-disable semi */
/// <reference types="cypress" />
// eslint-disable-next-line no-unused-vars
/* global cy Cypress describe beforeEach afterEach it */

// getting started guide:
// https://on.cypress.io/introduction-to-cypress

describe('reset-password page', () => {
  beforeEach(() => {
    // skip
  })
  afterEach(() => {
    // Here for any future use
  })

  it('allows entering email to request reset via email', () => {
    cy.visit('/password')
    cy.get('input[type=email]').type(`${Cypress.env('TEST_USERNAME')}`)
    cy.get('input[type=submit]').click()
    cy.get('[role=alert]').should('be.visible')
  })

  it('allows entering ninja name to request reset via email', () => {
    cy.visit('/password')
    cy.get('input[type=text]').type('glassbox')
    cy.get('input[type=submit]').click()
    cy.get('[role=alert]').should('be.visible')
  })

});
