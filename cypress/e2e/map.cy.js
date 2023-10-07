/* eslint-disable semi */
/// <reference types="cypress" />
// eslint-disable-next-line no-unused-vars
/* global cy Cypress describe beforeEach afterEach it */

// getting started guide:
// https://on.cypress.io/introduction-to-cypress

describe('check map loads', () => {
  beforeEach(() => {
    cy.standardLogin()
  })
  afterEach(() => {
  })

  it('displays map doshin', () => {
    cy.visit('/map')
    cy.contains('Map')
    cy.url().should('match', /map$/u)
    cy.get('[role=heading]').should('be.visible')
    cy.contains('Map').should('be.visible')
    cy.contains('Doshin').should('be.visible')
    cy.contains('Shrine').should('be.visible')
    cy.contains('Village Square').should('be.visible')
  })

  // it('displays the map even for logged out users', () => {
  //   cy.logout()
  //   cy.visit('/map')
  //   cy.contains('Map')
  //   cy.url().should('match', /map$/u)
  //   cy.get('[role=heading]').should('be.visible')
  //   cy.contains('Map').should('be.visible')
  //   cy.contains('Doshin').should('be.visible')
  //   cy.contains('Shrine').should('be.visible')
  //   cy.contains('Village Square').should('be.visible')
  // })
});
