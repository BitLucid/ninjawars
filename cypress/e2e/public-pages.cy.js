/* eslint-disable semi */
/// <reference types="cypress" />
// eslint-disable-next-line no-unused-vars
/* global cy Cypress describe beforeEach afterEach it */

// getting started guide:
// https://on.cypress.io/introduction-to-cypress

describe('check public pages load', () => {
  beforeEach(() => {
    // No login should be needed for public pages
  })
  afterEach(() => {
    // Not used at the moment
  })

  it('displays news page', () => {
    cy.visit('/news')
    cy.contains('News')
    cy.get('header').should('be.visible')
    cy.contains('News').should('be.visible')
  })

  it('displays intro page', () => {
    cy.visit('/intro/')
    cy.get('header').should('be.visible')
  })

  it('displays rules page', () => {
    cy.visit('/rules')
    cy.get('header').should('be.visible')
  })

  it('displays chat page', () => {
    cy.visit('/village')
    cy.get('header').should('be.visible')
  })
});
