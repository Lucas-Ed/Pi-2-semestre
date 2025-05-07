// document.addEventListener("DOMContentLoaded", function() {
//     const form = document.getElementById("formCarro");
//     if (!form) return;

//     form.addEventListener("submit", function(e) {
//         const modelo = document.getElementById("modelo").value.trim();
//         const placa = document.getElementById("placa").value.trim();
//         let erros = [];

//         if (modelo === "") erros.push("O campo 'modelo' é obrigatório.");
//         if (placa === "") erros.push("O campo 'placa' é obrigatório.");

//         if (erros.length > 0) {
//             e.preventDefault();
//             alert(erros.join("\n"));
//         }
//     });
// });
document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("carForm");
    if (!form) return;
  
    form.addEventListener("submit", async function (e) {
      e.preventDefault();
  
      const modelo = document.getElementById("modelo").value.trim();
      const placa = document.getElementById("placa").value.trim();
      let erros = [];
  
      // if (modelo === "") erros.push("O campo 'modelo' é obrigatório.");
      if (placa === "") erros.push("Placa inválida! Use o formato AAA-1234");
  
      if (erros.length > 0) {
        Swal.fire({
          icon: "warning",
          title: "Atenção",
          html: erros.join("<br>"),
        });
        return;
      }
  
      try {
        const response = await fetch("../public/api/addcar.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ modelo, placa }),
        });
  
        const result = await response.json();
  
        if (result.success) {
          Swal.fire({
            icon: "success",
            title: "Sucesso!",
            text: "Veículo cadastrado com sucesso!",
          }).then(() => {
            form.reset();
            const modal = bootstrap.Modal.getInstance(document.getElementById("addCarModal"));
            modal.hide();
          });
        } else {
          Swal.fire({
            icon: "error",
            title: "Erro",
            text: result.message || "Erro ao cadastrar veículo.",
          });
        }
      } catch (err) {
        Swal.fire({
          icon: "error",
          title: "Erro inesperado",
          text: "Não foi possível se conectar ao servidor.",
        });
      }
    });
  });
  