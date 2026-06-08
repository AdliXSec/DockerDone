from run import app
from app import db, bcrypt
from app.models.user_model import User

def seed_data():
    with app.app_context():
        db.drop_all()
        db.create_all()

        def get_hash(pw):
            return bcrypt.generate_password_hash(pw).decode('utf-8')

        dummy_users = [
            User(name="Admin User", email="admin@example.com", role="admin", password=get_hash("password123")),
            User(name="John Doe", email="john@example.com", role="user", password=get_hash("password123")),
            User(name="Jane Smith", email="jane@example.com", role="user", password=get_hash("password123")),
            User(name="Staff Member", email="staff@example.com", role="staff", password=get_hash("password123")),
        ]

        db.session.bulk_save_objects(dummy_users)
        db.session.commit()
        print("Database seeded successfully with hashed passwords!")

if __name__ == "__main__":
    seed_data()
