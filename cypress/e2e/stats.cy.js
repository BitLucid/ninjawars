/* eslint-disable semi */
/// <reference types="cypress" />
// eslint-disable-next-line no-unused-vars
/* global cy Cypress describe beforeEach afterEach it */

// getting started guide:
// https://on.cypress.io/introduction-to-cypress

describe('check stats for current ninja', () => {
  beforeEach(() => {
    cy.standardLogin()
  })
  afterEach(() => {
  })

  it('displays stats content', () => {
    cy.visit('/stats')
    // Stats heading should be present on page
    cy.contains('Stats')
    cy.url().should('match', /stats$/u)
    cy.get('[role=heading]').should('be.visible')
    cy.contains('Ninja Stats for').should('be.visible')
    cy.contains('Class:').should('be.visible')
  })
});
