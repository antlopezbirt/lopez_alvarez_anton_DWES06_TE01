package birt.daw.dwes06.apiusuarios.entity;

import org.hibernate.annotations.DynamicUpdate;

import jakarta.persistence.Column;
import jakarta.persistence.Entity;
import jakarta.persistence.GeneratedValue;
import jakarta.persistence.GenerationType;
import jakarta.persistence.Id;
import jakarta.persistence.Table;
import jakarta.validation.constraints.NotBlank;
import jakarta.validation.constraints.Size;
import lombok.AllArgsConstructor;
import lombok.Getter;
import lombok.NoArgsConstructor;
import lombok.Setter;

@Entity
@DynamicUpdate
@Table(name="usuario")
@NoArgsConstructor
@AllArgsConstructor
public class Usuario {
	
	@Id
	@GeneratedValue(strategy=GenerationType.IDENTITY)
	@Column(name="id")
	@Getter @Setter private int id;
	
	@NotBlank(message = "El nombre no puede estar vacío")
	@Size(min = 1, max = 50, message = "El nombre debe tener como mínimo 1 carácter y como máximo 50")
	@Column
	@Getter @Setter private String nombre;
	
	@NotBlank(message = "La extensión no puede estar vacía")
	@Size(min = 1, max = 50, message = "La extensión debe tener como mínimo 1 carácter y como máximo 50")
	@Column
	@Getter @Setter private String extension;
	
	@NotBlank(message = "La especialidad no puede estar vacía")
	@Size(min = 1, max = 50, message = "La especialidad debe tener como mínimo 1 carácter y como máximo 50")
	@Column
	@Getter @Setter private String especialidad;	
}
