describe('Cypress Test Case Management', () => {

    beforeEach(() => {

        cy.refreshDatabase()

        cy.create('App\\Models\\User', {
            name: 'Cypress Tester',
            email: 'cypress@example.com',
            password: 'password'
        })

        cy.login({
            attributes: {
                email: 'cypress@example.com'
            }
        })
    })


    it('can create a test case', () => {

        cy.visit('/test-cases')

        cy.contains('Cypress Test Cases')
            .should('be.visible')

        cy.contains('+ Add Test Case')
            .click()

        cy.url()
            .should('include', '/test-cases/create')

        cy.get('input[name="title"]')
            .type('User Login E2E Test')

        cy.get('input[name="module"]')
            .type('Authentication')

        cy.get('textarea[name="description"]')
            .type('Verify that a user can login successfully.')

        cy.get('select[name="priority"]')
            .select('high')

        cy.get('select[name="status"]')
            .select('active')

        cy.contains('Create Test Case')
            .click()

        cy.contains('Test case created successfully.')
            .should('be.visible')

        cy.contains('User Login E2E Test')
            .should('be.visible')
    })


    it('can search test cases', () => {

        cy.create('App\\Models\\TestCase', {
            title: 'Dashboard Access Test',
            module: 'Dashboard',
            description: 'Dashboard access verification.',
            priority: 'high',
            status: 'active'
        })

        cy.create('App\\Models\\TestCase', {
            title: 'Profile Update Test',
            module: 'Profile',
            description: 'Profile update verification.',
            priority: 'medium',
            status: 'active'
        })

        cy.visit('/test-cases')

        cy.get('input[name="search"]')
            .type('Dashboard')

        cy.contains('Go')
            .click()

        cy.contains('Dashboard Access Test')
            .should('be.visible')

        cy.contains('Profile Update Test')
            .should('not.exist')
    })


    it('can edit a test case', () => {

        cy.create('App\\Models\\TestCase', {
            title: 'Original Test Case',
            module: 'Authentication',
            description: 'Original description.',
            priority: 'medium',
            status: 'active'
        })

        cy.visit('/test-cases')

        cy.contains('Original Test Case')
            .should('be.visible')
            .closest('tr')
            .within(() => {

                cy.contains('Edit')
                    .click()
            })

        cy.url()
            .should('include', '/test-cases/')

        cy.get('input[name="title"]')
            .should('have.value', 'Original Test Case')

        cy.get('input[name="title"]')
            .clear()
            .type('Updated Test Case')

        cy.get('select[name="priority"]')
            .select('high')

        cy.contains('Update Test Case')
            .click()

        cy.contains('Test case updated successfully.')
            .should('be.visible')

        cy.contains('Updated Test Case')
            .should('be.visible')
    })


it('can delete a test case', () => {

    cy.create('App\\Models\\TestCase', {
        title: 'Delete Test Case',
        module: 'Testing',
        description: 'Test deletion.',
        priority: 'low',
        status: 'active'
    })

    cy.visit('/test-cases')

    cy.contains('Delete Test Case')
        .should('be.visible')
        .closest('tr')
        .within(() => {

            cy.get('form')
                .find('button[type="submit"]')
                .invoke('removeAttr', 'onclick')
                .click()

        })

    cy.url()
        .should('match', /\/test-cases$/)

    cy.contains('Delete Test Case')
        .should('not.exist')
})

})