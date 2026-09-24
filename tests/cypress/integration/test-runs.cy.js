describe('Cypress Test Run Analytics', () => {

    beforeEach(() => {

        cy.refreshDatabase()

        cy.create('App\\Models\\User', {
            name: 'Analytics Tester',
            email: 'analytics@example.com',
            password: 'password'
        })

        cy.login({
            attributes: {
                email: 'analytics@example.com'
            }
        })

    })


    it('displays test run statistics', () => {

        cy.request({
            method: 'POST',
            url: '/test-runs',
            body: {
                spec_name: 'login.cy.js',
                test_name: 'User can login',
                status: 'passed',
                browser: 'Chrome',
                duration: 1200
            }
        })

        cy.request({
            method: 'POST',
            url: '/test-runs',
            body: {
                spec_name: 'profile.cy.js',
                test_name: 'User can update profile',
                status: 'passed',
                browser: 'Chrome',
                duration: 900
            }
        })

        cy.request({
            method: 'POST',
            url: '/test-runs',
            body: {
                spec_name: 'dashboard.cy.js',
                test_name: 'Dashboard access',
                status: 'failed',
                browser: 'Chrome',
                duration: 1500,
                error_message: 'Dashboard assertion failed.'
            }
        })

        cy.visit('/test-runs/dashboard')

        cy.contains('Cypress Test Analytics')
            .should('be.visible')

        cy.contains('Total Tests')
            .should('be.visible')

        cy.contains('2')
            .should('be.visible')

        cy.contains('Passed')
            .should('be.visible')

        cy.contains('Failed')
            .should('be.visible')

        cy.contains('66.67%')
            .should('be.visible')

    })


    it('can filter test run history', () => {

        cy.request({
            method: 'POST',
            url: '/test-runs',
            body: {
                spec_name: 'login.cy.js',
                test_name: 'Login test',
                status: 'passed',
                browser: 'Chrome',
                duration: 800
            }
        })

        cy.request({
            method: 'POST',
            url: '/test-runs',
            body: {
                spec_name: 'profile.cy.js',
                test_name: 'Profile test',
                status: 'failed',
                browser: 'Chrome',
                duration: 1000,
                error_message: 'Profile assertion failed.'
            }
        })

        cy.visit('/test-runs')

        cy.get('select[name="status"]')
            .select('failed')

        cy.contains('Filter')
            .click()

        cy.contains('Profile test')
            .should('be.visible')

        cy.contains('Login test')
            .should('not.exist')

    })

})