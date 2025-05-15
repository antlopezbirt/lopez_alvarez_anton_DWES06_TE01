package birt.daw.dwes06.apiusuarios.dao;

import java.util.List;

import org.hibernate.Session;
import org.hibernate.query.IllegalMutationQueryException;
import org.hibernate.query.MutationQuery;
import org.hibernate.query.SelectionQuery;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Repository;

import birt.daw.dwes06.apiusuarios.entity.Usuario;
import jakarta.persistence.EntityManager;
import jakarta.transaction.Transactional;

@Repository
public class UsuarioDAOImpl implements UsuarioDAO {
	
	@Autowired
	private EntityManager em;
	
	@Override
	@Transactional
	public List<Usuario> getAll() {
		Session currentSession = em.unwrap(Session.class);
		SelectionQuery<Usuario> consulta = currentSession.createSelectionQuery("from Usuario", Usuario.class);
		List<Usuario> usuarios = consulta.getResultList();
		return usuarios;
	}

	@Override
	@Transactional
	public Usuario getById(int idUsuario) {
		Session currentSession = em.unwrap(Session.class);
		Usuario usuario = currentSession.get(Usuario.class, idUsuario);
		return usuario;
	}

	@Override
	@Transactional
	public void create(Usuario usuario) {
		Session currentSession = em.unwrap(Session.class);
		currentSession.persist(usuario);
	}
	
	@Override
	@Transactional
	public void update(Usuario usuario) {
		Session currentSession = em.unwrap(Session.class);
		currentSession.merge(usuario);
	}

	@Override
	@Transactional
	public void deleteById(int idUsuario) {
		Session currentSession = em.unwrap(Session.class);
		try {
			MutationQuery consulta = currentSession.createMutationQuery("DELETE FROM Usuario WHERE id = ?1");
			consulta.setParameter(1, idUsuario);
			consulta.executeUpdate();
//		    fail("Expected an IllegalMutationQueryException");
		} catch (IllegalMutationQueryException e) {
//		    log.info("Hibernate threw the expected IllegalMutationQueryException.");
//		    log.info(e);
		}
	}
}
