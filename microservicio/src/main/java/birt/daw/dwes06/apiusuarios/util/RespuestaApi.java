package birt.daw.dwes06.apiusuarios.util;

import java.util.List;

import lombok.AllArgsConstructor;
import lombok.Data;

@Data
@AllArgsConstructor
public class RespuestaApi {
	private String status;
	private int code;
	private String description;
	private List<?> data;
}