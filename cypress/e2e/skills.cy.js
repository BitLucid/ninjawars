/* eslint-disable semi */
/// <reference types="cypress" />
// eslint-disable-next-line no-unused-vars
/* global cy Cypress describe beforeEach afterEach it */

// getting started guide:
// https://on.cypress.io/introduction-to-cypress

describe('check skills for current ninja', () => {
  beforeEach(() => {
    cy.standardLogin()
  })
  afterEach(() => {
  })

  it('displays skills', () => {
    cy.visit('/skill')
    cy.contains('Skills')
    cy.url().should('match', /skill$/u)
    cy.get('[role=heading]').contains('Skills').should('be.visible')
    cy.contains('Dueling Combat Skills').should('be.visible')
    cy.contains('Passive Skills').should('be.visible')
  })
});
