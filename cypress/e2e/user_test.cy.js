describe("Gestion des utilisateurs", () => {
    beforeEach(() => {
        cy.visit("http://localhost:8000"); 
    });

    it("Ajout d'un utilisateur", () => {
        cy.get("#name").type("Alice");
        cy.get("#email").type("alice@example.com");
        cy.get("button[type='submit']").click();

        cy.get("#userList").should("contain.text", "Alice (alice@example.com)");
    });

    it("Modification d'un utilisateur", () => {
        cy.contains("Alice (alice@example.com)").should("exist");

        cy.contains("Alice (alice@example.com)").parent().find("button").first().click();

        cy.get("#name").clear().type("Alice Updated");
        cy.get("#email").clear().type("alice.updated@example.com");
        cy.get("button[type='submit']").click();

        cy.contains("Alice Updated (alice.updated@example.com)").should("exist");
    });

    it("Suppression d'un utilisateur", () => {
        cy.request('GET', 'http://localhost:8000/api.php').then((response) => {
            const users = response.body;
            const userToDelete = users.find(user => user.email === "alice.updated@example.com");
    
            if (!userToDelete) {
                throw new Error("ID non trouvé pour Alice Updated !");
            }
    
            cy.log(`ID récupéré pour suppression : ${userToDelete.id}`);
    
            cy.request('DELETE', `http://localhost:8000/api.php?id=${userToDelete.id}`).then((deleteResponse) => {
                expect(deleteResponse.status).to.eq(200);
    
                cy.request('GET', 'http://localhost:8000/api.php').then((newResponse) => {
                    const updatedUsers = newResponse.body;
                    const stillExists = updatedUsers.some(user => user.id === userToDelete.id);
                    expect(stillExists).to.be.false;
                });
            });
        });
    });    
});
