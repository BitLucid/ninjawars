/* eslint-disable max-lines-per-function */
/* eslint-disable padded-blocks */
/* eslint-disable semi */
/// <reference types="cypress" />
// eslint-disable-next-line no-unused-vars
/* global cy Cypress describe beforeEach afterEach it */

// getting started guide:
// https://on.cypress.io/introduction-to-cypress

describe('signup a new ninja', () => {
  beforeEach(() => {
    // cy.standardLogin()
  })
  afterEach(() => {
  })

  it('rejects signup for configured user, as should already exist', () => {
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
    cy.get('button#reveal-signup').click()
    const submitButton = 'input[type=submit]'
    cy.contains(submitButton).should('be.visible')
    cy.get(submitButton).click()
    cy.get('[role=alert]').should('be.visible')
  })

  it('allows custom login of the test user', () => {
    cy.visit('/login')
    cy.customLogin(Cypress.env('TEST_USERNAME'), Cypress.env('TEST_PASSWORD'))
    cy.get('.login-form').should('not.exist')
    cy.log('Checking for the incorrect username/password alert...')
    cy.get('[role=alert]').should('not.exist')

  })

  it('allows signup for a randomized ninja', () => {
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
    cy.get('button#reveal-signup').click()
    const submitButton = 'input[type=submit]'
    cy.contains(submitButton).should('be.visible')
    cy.get(submitButton).click()
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
