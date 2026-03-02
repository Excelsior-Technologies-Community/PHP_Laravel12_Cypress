describe('Dashboard Test', () => {

    beforeEach(() => {
        cy.refreshDatabase()
    })

    it('User can login without UI', () => {

        // Create user
        cy.create('App\\Models\\User', {
            name: 'Harry',
            email: 'test@example.com',
            password: 'password'
        })

        // Direct login (no form typing)
        cy.login({ attributes: { email: 'test@example.com' } })

        // Visit dashboard
        cy.visit('/dashboard')

        // Assertion
        cy.contains('Dashboard')

    })

})