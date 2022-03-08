Vue.createApp({
  delimiters: ["${", "}"],
  data() {
    return {
      toDoList: [],
      newTodo: "",
      editTodo: "",
    };
  },
  methods: {
    todo_fetch() {
      var self = this;
      axios.get("/fetch").then(function (response) {
        if (response.data.status) {
          self.toDoList = response.data.data;
        } else {
          throw new Error("Something wrong to fetch data!");
        }
      });
    },
    async todo_add() {
      await axios.post("/add", { name: this.newTodo });
      this.newTodo = "";
      this.todo_fetch();
    },
    async todo_remove(rowId) {
      await axios.post("/remove", { id: rowId });
      this.todo_fetch();
    },
    async todo_update(rowId, fieldname, value) {
      const updata = { id: rowId };
      updata[fieldname] = value;
      await axios.post("/update", updata);
      this.editTodo = "";
      this.todo_fetch();
    },
    click_edit($ev, rowId, index) {
      $($ev.target).closest("tr").addClass("mode_edit");
      this.editTodo = (this.toDoList[index] !== undefined) ? this.toDoList[index].name : '';
      $($ev.target).closest("tr").find(".mode_edit_text").val(this.editTodo);
    },
    async click_save($ev, rowId) {
      await this.todo_update(rowId, 'name', this.editTodo);
      $($ev.target).closest("tr").removeClass("mode_edit");
    },
  },
  mounted() {
    this.todo_fetch();
  },
}).mount("#app");
