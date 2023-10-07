/* eslint-disable semi */
/// <reference types="cypress" />
// eslint-disable-next-line no-unused-vars
/* global cy Cypress describe beforeEach afterEach it */

// getting started guide:
// https://on.cypress.io/introduction-to-cypress

describe('fight', () => {
  beforeEach(() => {
    cy.standardLogin()
  })
  afterEach(() => {
  })

  it('displays someone to fight', () => {
    cy.visit('/enemies')
    cy.contains('Fight')
    cy.url().should('match', /enemies$/u)
    cy.get('[role=heading]').contains('Fight').should('be.visible')
    cy.contains('Attack').should('be.visible')
    cy.get('.avatar').should('be.visible')
  })

  // NPC checks
  it('can attack a thief', () => {
    cy.visit('/enemies')
    cy.url().should('match', /enemies$/u)
    cy.get('[role=heading]').contains('Fight').should('be.visible')
    cy.contains('Attack a').should('be.visible')
    cy.get('a[href*="/npc/attack/thief"]').first().click()
    cy.contains('sees you and prepares to defend!').should('be.visible')
    // Hmm, will fail on random group of thieves
  })
});
